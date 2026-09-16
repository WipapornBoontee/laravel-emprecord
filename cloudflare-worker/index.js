export default {
  async fetch(request, env, ctx) {
    const NGROK_URL = env.NGROK_URL || "https://wad-attic-catwalk.ngrok-free.dev";

    try {
      const url = new URL(request.url);
      const targetUrl = new URL(url.pathname + url.search, NGROK_URL);

      const newHeaders = new Headers(request.headers);
      newHeaders.set("ngrok-skip-browser-warning", "true");
      newHeaders.set("Host", targetUrl.host);
      newHeaders.set("X-Forwarded-Host", url.host);
      newHeaders.set("X-Forwarded-Proto", url.protocol.replace(":", ""));

      const modifiedRequest = new Request(targetUrl.toString(), {
        method: request.method,
        headers: newHeaders,
        body: ["GET", "HEAD"].includes(request.method) ? null : request.body,
        redirect: "manual",
      });

      const response = await fetch(modifiedRequest);

      const responseHeaders = new Headers(response.headers);
      const location = responseHeaders.get("Location");
      if (location) {
        responseHeaders.set("Location", location.replace(NGROK_URL, url.origin).replace(targetUrl.host, url.host));
      }

      // Preserve multiple Set-Cookie headers (session, XSRF, remember_web, remember_username)
      if (typeof response.headers.getSetCookie === "function") {
        responseHeaders.delete("set-cookie");
        for (const cookie of response.headers.getSetCookie()) {
          responseHeaders.append("set-cookie", cookie);
        }
      }

      const contentType = responseHeaders.get("content-type") || "";
      if (contentType.includes("text/html") || contentType.includes("application/json")) {
        let text = await response.text();
        text = text.replaceAll(NGROK_URL, url.origin);
        text = text.replaceAll(targetUrl.host, url.host);
        return new Response(text, {
          status: response.status,
          statusText: response.statusText,
          headers: responseHeaders,
        });
      }

      return new Response(response.body, {
        status: response.status,
        statusText: response.statusText,
        headers: responseHeaders,
      });
    } catch (err) {
      return new Response("Proxy Error: " + err.message, { status: 502 });
    }
  },
};
