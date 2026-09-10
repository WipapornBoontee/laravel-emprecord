export default {
  async fetch(request, env, ctx) {
    const NGROK_URL = env.NGROK_URL || "https://garter-refusal-improve.ngrok-free.dev";

    try {
      const url = new URL(request.url);
      const targetUrl = new URL(url.pathname + url.search, NGROK_URL);

      // Clone headers และแนบ Header เพื่อข้ามหน้า Browser Warning ของ ngrok
      const newHeaders = new Headers(request.headers);
      newHeaders.set("ngrok-skip-browser-warning", "true");
      newHeaders.set("Host", targetUrl.host);

      // Forward request ไปยัง ngrok
      const modifiedRequest = new Request(targetUrl.toString(), {
        method: request.method,
        headers: newHeaders,
        body: ["GET", "HEAD"].includes(request.method) ? null : request.body,
        redirect: "follow",
      });

      const response = await fetch(modifiedRequest);
      return response;
    } catch (err) {
      return new Response("Proxy Error: " + err.message, { status: 502 });
    }
  },
};
