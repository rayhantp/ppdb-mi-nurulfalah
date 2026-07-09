<div id="loading-overlay" class="fixed inset-0 z-[9999] flex items-center justify-center bg-white/70 backdrop-blur-md hidden transition-opacity duration-500">
    <div class="text-center">
        <div class="relative inline-block">
            <div class="absolute inset-0 bg-green-200 rounded-full blur-xl opacity-50 animate-ping"></div>
            <img src="assets/img/logo.png" alt="Logo" class="relative w-20 h-20 md:w-24 md:h-24 object-contain animate-bounce">
        </div>
        
        <h2 class="mt-6 text-green-800 font-bold text-lg tracking-wide animate-pulse">
            Mohon Tunggu...
        </h2>
        <p class="text-slate-400 text-xs font-medium uppercase tracking-[0.3em] mt-2">MI Nurul Falah</p>
        
        <div class="mt-8 w-48 h-1.5 bg-slate-100 rounded-full mx-auto overflow-hidden">
            <div class="h-full bg-green-600 rounded-full animate-[loading_2s_ease-in-out_infinite]"></div>
        </div>
    </div>
</div>

<style>
    @keyframes loading {
        0% { width: 0%; transform: translateX(-100%); }
        50% { width: 100%; transform: translateX(0%); }
        100% { width: 0%; transform: translateX(100%); }
    }
</style>