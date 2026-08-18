<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>เกี่ยวกับฉัน | Kitsanaphong</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Inter & Kanit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', 'Kanit', sans-serif;
            background: radial-gradient(circle at 50% 50%, #1a102f 0%, #0b0616 100%);
        }
        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px rgba(255, 255, 255, 0.08) solid;
        }
    </style>
</head>
<body class="text-slate-100 min-h-screen flex flex-col justify-between overflow-x-hidden selection:bg-purple-500 selection:text-white">

    <!-- Glowing background blobs -->
    <div class="absolute top-[-20%] left-[-10%] w-[500px] h-[500px] rounded-full bg-purple-900/20 blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[500px] h-[500px] rounded-full bg-pink-900/20 blur-[120px] pointer-events-none"></div>

    <!-- Header Navigation -->
    <header class="w-full max-w-6xl mx-auto px-6 py-5 flex justify-between items-center z-10">
        <a href="/" class="text-xl font-bold tracking-wider text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-400 hover:scale-105 transition-transform duration-200">
            KITSANAPHONG.KU
        </a>
        <nav class="flex items-center gap-6 glass px-6 py-2 rounded-full">
            <a href="/welcome" class="text-sm font-medium text-slate-300 hover:text-purple-300 transition-colors">หน้าแรก</a>
            <a href="/blog" class="text-sm font-medium text-slate-300 hover:text-purple-300 transition-colors">บทความ</a>
            <a href="/about" class="text-sm font-medium text-purple-300 hover:text-purple-200 transition-colors">เกี่ยวกับฉัน</a>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="w-full max-w-3xl mx-auto px-6 py-12 z-10 flex-grow flex flex-col justify-center">
        <div class="glass rounded-3xl p-8 md:p-12 shadow-2xl relative overflow-hidden">
            <!-- Glow Accent inside card -->
            <div class="absolute -top-12 -right-12 w-40 h-40 rounded-full bg-pink-500/10 blur-xl pointer-events-none"></div>

            <div class="flex flex-col items-center md:items-start md:flex-row gap-8">
                <!-- Avatar / Badge -->
                <div class="flex-shrink-0">
                    <div class="w-32 h-32 md:w-40 md:h-40 rounded-3xl bg-gradient-to-tr from-purple-500 via-pink-500 to-amber-400 flex items-center justify-center text-4xl md:text-5xl font-extrabold text-white shadow-xl shadow-purple-500/20 border border-white/20 select-none">
                        TF
                    </div>
                </div>

                <!-- Profile Info -->
                <div class="flex-grow text-center md:text-left">
                    <h1 class="text-3xl md:text-4xl font-extrabold text-white mb-2">ท็อปฟี่ (Toffy)</h1>
                    <p class="text-purple-300 text-sm font-semibold tracking-wider uppercase mb-4">Web Developer & Creator</p>
                    <p class="text-slate-300 text-base leading-relaxed mb-6">
                        สวัสดีครับ! ผมท็อปฟี่ ยินดีต้อนรับสู่เว็บไซต์ของผมครับ ผมมีความสนใจและหลงใหลในการพัฒนาเว็บไซต์ด้วยเทคโนโลยีที่ทันสมัย อย่าง Laravel, PHP, JavaScript และการออกแบบ User Interface ที่สวยงาม ตอบโจทย์การใช้งานของผู้ใช้อย่างแท้จริง
                    </p>

                    <!-- Profile Stats / Badges -->
                    <div class="flex flex-wrap gap-2 justify-center md:justify-start">
                        <span class="px-3.5 py-1.5 text-xs font-semibold rounded-full bg-white/5 border border-white/10 text-slate-300 hover:bg-white/10 transition-all">💻 HTML / CSS</span>
                        <span class="px-3.5 py-1.5 text-xs font-semibold rounded-full bg-white/5 border border-white/10 text-slate-300 hover:bg-white/10 transition-all">⚡ JavaScript</span>
                        <span class="px-3.5 py-1.5 text-xs font-semibold rounded-full bg-white/5 border border-white/10 text-slate-300 hover:bg-white/10 transition-all">🐘 PHP</span>
                        <span class="px-3.5 py-1.5 text-xs font-semibold rounded-full bg-white/5 border border-white/10 text-slate-300 hover:bg-white/10 transition-all">🔥 Laravel 12</span>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div class="border-t border-white/10 my-8"></div>

            <!-- More Info Section -->
            <div class="grid md:grid-cols-2 gap-6 text-slate-300">
                <div class="glass p-5 rounded-2xl">
                    <h3 class="text-white font-semibold mb-2 flex items-center gap-2">
                        <span class="w-1.5 h-4 bg-purple-500 rounded-full"></span>
                        เป้าหมายของฉัน
                    </h3>
                    <p class="text-sm leading-relaxed text-slate-400">
                        สร้างสรรค์เว็บไซต์และเว็บแอปพลิเคชันที่มีประสิทธิภาพสูง สวยงามสะดุดตา และมอบประสบการณ์การใช้งานที่ลื่นไหลระดับพรีเมียม
                    </p>
                </div>
                <div class="glass p-5 rounded-2xl">
                    <h3 class="text-white font-semibold mb-2 flex items-center gap-2">
                        <span class="w-1.5 h-4 bg-pink-500 rounded-full"></span>
                        ช่องทางการติดต่อ
                    </h3>
                    <p class="text-sm leading-relaxed text-slate-400">
                        สามารถเข้ามาพูดคุย แลกเปลี่ยน หรือติดตามการพัฒนาเว็บแอปพลิเคชันต่างๆ ได้ที่หน้าแรกหรือบทความของผมครับ!
                    </p>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full text-center py-8 text-slate-500 text-sm border-t border-slate-900 mt-12 z-10">
        <p>&copy; 2026 Kitsanaphong. All rights reserved.</p>
    </footer>

</body>
</html>