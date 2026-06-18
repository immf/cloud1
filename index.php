<?php $apiUrl = rtrim(dirname($_SERVER['PHP_SELF']), '/') . '/api.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="mobile-web-app-capable" content="yes">
<meta name="theme-color" content="#09090f">
<title>Rockify</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@800;900&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
<script>
tailwind.config = {
  theme: { extend: {
    fontFamily: { sans: ['Inter','system-ui','sans-serif'], display: ['"Exo 2"','sans-serif'] },
    colors: { rock: { 400:'#c084fc', 500:'#a855f7', 600:'#9333ea' } }
  }}
}
</script>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{background:#09090f;color:#fff;font-family:'Inter',sans-serif;overflow:hidden;height:100vh}
::-webkit-scrollbar{width:5px;height:5px}
::-webkit-scrollbar-track{background:transparent}
::-webkit-scrollbar-thumb{background:#333;border-radius:3px}

/* ── Layout ── */
#app{display:grid;height:100dvh;grid-template-areas:'sidebar main''player player';grid-template-columns:240px 1fr;grid-template-rows:1fr 88px}
@media(max-width:768px){
  #app{grid-template-areas:'main''player';grid-template-columns:1fr;grid-template-rows:1fr 72px}
}
.area-sidebar{grid-area:sidebar}
.area-main{grid-area:main}
.area-player{grid-area:player}

/* ── Psychedelic background orbs ── */
.orb{position:fixed;border-radius:50%;filter:blur(130px);opacity:.18;pointer-events:none;z-index:0;transition:background 5s ease}
.orb-1{width:700px;height:700px;top:-280px;left:-200px;animation:o1 34s ease-in-out infinite}
.orb-2{width:650px;height:650px;bottom:-280px;right:-200px;animation:o2 28s ease-in-out infinite}
.orb-3{width:500px;height:500px;top:35%;right:5%;animation:o3 22s ease-in-out infinite}
.orb-4{width:450px;height:450px;bottom:5%;left:15%;animation:o4 38s ease-in-out infinite}
@keyframes o1{0%,100%{transform:translate(0,0) scale(1)}25%{transform:translate(180px,120px) scale(1.1)}50%{transform:translate(350px,-60px) scale(.9)}75%{transform:translate(100px,260px) scale(1.2)}}
@keyframes o2{0%,100%{transform:translate(0,0) scale(1)}33%{transform:translate(-220px,-110px) scale(1.15)}66%{transform:translate(120px,160px) scale(.85)}}
@keyframes o3{0%,100%{transform:translate(0,0) scale(1)}50%{transform:translate(-280px,120px) scale(1.35)}}
@keyframes o4{0%,100%{transform:translate(0,0) scale(1)}25%{transform:translate(210px,-160px) scale(.8)}75%{transform:translate(-120px,-210px) scale(1.25)}}

/* ── Sidebar ── */
.sidebar-wrap{background:rgba(9,9,15,.88);backdrop-filter:blur(24px);border-right:1px solid rgba(255,255,255,.06);overflow-y:auto;position:relative;z-index:10}
@media(max-width:768px){
  .sidebar-wrap{position:fixed;top:0;left:0;bottom:72px;width:280px;transform:translateX(-110%);transition:transform .3s ease;z-index:100;border-right:1px solid rgba(255,255,255,.1)}
  .sidebar-wrap.open{transform:translateX(0)}
}
.sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:99}
@media(max-width:768px){.sidebar-overlay{display:block}}

.nav-btn{display:flex;align-items:center;gap:12px;width:100%;padding:10px 12px;border-radius:8px;font-size:14px;font-weight:500;color:#a0a0b8;transition:all .15s;cursor:pointer;border:none;background:transparent}
.nav-btn:hover{background:rgba(255,255,255,.07);color:#fff}
.nav-btn.active{background:rgba(168,85,247,.18);color:#c084fc}

/* ── Cards ── */
.album-card{background:rgba(255,255,255,.05);border-radius:10px;padding:14px;cursor:pointer;transition:background .2s,transform .2s;position:relative}
.album-card:hover{background:rgba(255,255,255,.1);transform:translateY(-3px)}
.album-card .play-btn{position:absolute;bottom:18px;right:14px;width:40px;height:40px;background:#a855f7;border-radius:50%;display:flex;align-items:center;justify-content:center;opacity:0;transform:translateY(6px);transition:all .2s;box-shadow:0 4px 20px rgba(168,85,247,.5);border:none;cursor:pointer}
.album-card:hover .play-btn{opacity:1;transform:translateY(0)}
.album-card .play-btn:hover{background:#c084fc;transform:translateY(0) scale(1.08)}

/* ── Track rows ── */
.track-row{display:flex;align-items:center;gap:12px;padding:8px 12px;border-radius:8px;cursor:pointer;transition:background .15s}
.track-row:hover{background:rgba(255,255,255,.06)}
.track-row.playing{background:rgba(168,85,247,.15)}

/* ── Waveform ── */
.wave{display:flex;align-items:flex-end;gap:2px;height:16px}
.wave span{width:3px;background:#a855f7;border-radius:2px;animation:wv .8s ease-in-out infinite}
.wave span:nth-child(1){animation-delay:0s}
.wave span:nth-child(2){animation-delay:.15s}
.wave span:nth-child(3){animation-delay:.3s}
.wave span:nth-child(4){animation-delay:.45s}
@keyframes wv{0%,100%{height:4px}50%{height:14px}}

/* ── Player bar ── */
.player-wrap{background:rgba(9,9,15,.96);backdrop-filter:blur(32px);border-top:1px solid rgba(255,255,255,.07);position:relative;z-index:20}

/* ── Range inputs ── */
input[type=range]{-webkit-appearance:none;appearance:none;background:transparent;cursor:pointer}
input[type=range]::-webkit-slider-runnable-track{height:4px;border-radius:2px;background:rgba(255,255,255,.2)}
input[type=range]::-webkit-slider-thumb{-webkit-appearance:none;width:12px;height:12px;border-radius:50%;background:#fff;margin-top:-4px;opacity:0;transition:opacity .15s}
input[type=range]:hover::-webkit-slider-thumb{opacity:1}
input[type=range].vol::-webkit-slider-thumb{opacity:1}

/* ── Gradient text ── */
.gtext{background:linear-gradient(135deg,#c084fc,#f472b6);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}

/* ── Transitions ── */
.fade-enter-active,.fade-leave-active{transition:opacity .25s}
.fade-enter-from,.fade-leave-to{opacity:0}
.slide-up-enter-active,.slide-up-leave-active{transition:transform .3s ease}
.slide-up-enter-from,.slide-up-leave-to{transform:translateY(100%)}
.slide-r-enter-active,.slide-r-leave-active{transition:transform .3s ease}
.slide-r-enter-from,.slide-r-leave-to{transform:translateX(100%)}

/* ── No cover placeholder ── */
.no-cover{background:linear-gradient(135deg,#1e0a3c,#0a1e3c);display:flex;align-items:center;justify-content:center}

/* ── Mobile top bar ── */
.mobile-top{display:none;align-items:center;gap:12px;padding:12px 16px;background:rgba(9,9,15,.9);backdrop-filter:blur(20px);border-bottom:1px solid rgba(255,255,255,.05);position:sticky;top:0;z-index:30}
@media(max-width:768px){.mobile-top{display:flex}}

/* ── Vinyl spin ── */
@keyframes vinyl{from{transform:rotate(0)}to{transform:rotate(360deg)}}
.vinyl-spin{animation:vinyl 4s linear infinite}
.vinyl-pause{animation-play-state:paused}

/* ── Gradient progress fill trick ── */
.prog-wrap{position:relative;height:4px;border-radius:2px;background:rgba(255,255,255,.2);cursor:pointer}
.prog-fill{position:absolute;left:0;top:0;height:100%;border-radius:2px;background:linear-gradient(90deg,#a855f7,#ec4899);pointer-events:none;transition:width .1s linear}
.prog-wrap input[type=range]{position:absolute;inset:0;width:100%;height:100%;opacity:0;margin:0}

/* ── Hero gradient on album/artist pages ── */
.hero-gradient{background:linear-gradient(to bottom,var(--hc,#2a0050) 0%,transparent 100%)}
</style>
</head>
<body>
<div id="app">

<!-- ═══ BACKGROUND ORBS ═══ -->
<div class="fixed inset-0" style="background:#09090f;z-index:0;overflow:hidden">
  <div class="orb orb-1" :style="{background:bgColors[0]}"></div>
  <div class="orb orb-2" :style="{background:bgColors[1]}"></div>
  <div class="orb orb-3" :style="{background:bgColors[2]}"></div>
  <div class="orb orb-4" :style="{background:bgColors[3]}"></div>
</div>

<!-- ═══ SIDEBAR OVERLAY (mobile) ═══ -->
<transition name="fade">
  <div v-if="sidebarOpen" class="sidebar-overlay" @click="sidebarOpen=false" style="z-index:99"></div>
</transition>

<!-- ═══ SIDEBAR ═══ -->
<aside class="area-sidebar sidebar-wrap" :class="{open:sidebarOpen}">
  <!-- Logo -->
  <div class="px-5 pt-6 pb-5 flex items-center gap-2">
    <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
      <circle cx="14" cy="14" r="14" fill="url(#lg1)"/>
      <circle cx="14" cy="14" r="5" fill="#09090f"/>
      <circle cx="14" cy="14" r="2" fill="#a855f7"/>
      <path d="M14 4 A10 10 0 0 1 24 14" stroke="#f472b6" stroke-width="2" fill="none" stroke-linecap="round"/>
      <defs><linearGradient id="lg1" x1="0" y1="0" x2="28" y2="28" gradientUnits="userSpaceOnUse"><stop stop-color="#7c3aed"/><stop offset="1" stop-color="#ec4899"/></linearGradient></defs>
    </svg>
    <span class="font-display font-black text-xl gtext tracking-wide">ROCKIFY</span>
  </div>

  <!-- Navigation -->
  <div class="px-3 space-y-1">
    <button class="nav-btn" :class="{active:view==='home'}" @click="gotoHome()">
      <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
      Início
    </button>
    <button class="nav-btn" :class="{active:view==='search'}" @click="gotoSearch()">
      <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
      Buscar
    </button>
  </div>

  <!-- Library header -->
  <div class="px-5 mt-6 mb-2 flex items-center justify-between">
    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Biblioteca</span>
    <button @click="startCreatePlaylist()" class="text-gray-500 hover:text-white transition-colors" title="Nova playlist">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
    </button>
  </div>

  <!-- All Music -->
  <div class="px-3 space-y-0.5">
    <button class="nav-btn" :class="{active:view==='home'}" @click="gotoHome()">
      <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3v9.28a4.39 4.39 0 0 0-1.5-.28C8.01 12 6 14.01 6 16.5S8.01 21 10.5 21c2.31 0 4.2-1.75 4.45-4H15V6h4V3h-7z"/></svg>
      Todas as músicas
    </button>

    <!-- Playlists -->
    <template v-for="pl in playlists" :key="pl.id">
      <button class="nav-btn" :class="{active:view==='playlist'&&selectedPlaylistId===pl.id}" @click="gotoPlaylist(pl.id)">
        <svg class="w-5 h-5 flex-shrink-0 text-purple-400" viewBox="0 0 24 24" fill="currentColor"><path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z"/></svg>
        <span class="truncate">{{ pl.name }}</span>
      </button>
    </template>

    <!-- Create playlist input -->
    <div v-if="creatingPlaylist" class="px-2 py-1">
      <input v-model="newPlaylistName" @keyup.enter="confirmCreatePlaylist()" @keyup.esc="creatingPlaylist=false"
        class="w-full bg-white/10 text-white text-sm rounded px-2 py-1 outline-none border border-purple-500/50 focus:border-purple-400"
        placeholder="Nome da playlist..." autofocus ref="plInput">
    </div>
  </div>

  <!-- Artists section -->
  <div class="px-5 mt-5 mb-2">
    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Artistas</span>
  </div>
  <div class="px-3 space-y-0.5 pb-4">
    <button v-for="art in library?.artists||[]" :key="art.name"
      class="nav-btn text-left" :class="{active:view==='artist'&&selectedArtistName===art.name}"
      @click="gotoArtist(art.name)">
      <div class="w-5 h-5 rounded-full bg-gradient-to-br from-purple-600 to-pink-500 flex-shrink-0 flex items-center justify-center text-xs font-bold">
        {{ art.name.charAt(0).toUpperCase() }}
      </div>
      <span class="truncate">{{ art.name }}</span>
    </button>
  </div>
</aside>

<!-- ═══ MAIN CONTENT ═══ -->
<main class="area-main" style="overflow-y:auto;position:relative;z-index:5">

  <!-- Mobile top bar -->
  <div class="mobile-top">
    <button @click="sidebarOpen=!sidebarOpen" class="text-gray-400 hover:text-white">
      <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/></svg>
    </button>
    <span class="font-display font-black text-lg gtext">ROCKIFY</span>
    <button v-if="currentTrack" @click="showExpandedPlayer=true" class="ml-auto text-gray-400 hover:text-white">
      <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 15.41L12 10.83l4.59 4.58L18 14l-6-6-6 6z"/></svg>
    </button>
  </div>

  <!-- Loading -->
  <div v-if="loading" class="flex flex-col items-center justify-center h-64 gap-4">
    <div class="w-12 h-12 border-2 border-purple-500/30 border-t-purple-500 rounded-full animate-spin"></div>
    <p class="text-gray-400 text-sm">Carregando biblioteca...</p>
  </div>

  <!-- Error -->
  <div v-else-if="error" class="flex flex-col items-center justify-center h-64 gap-3 px-6 text-center">
    <svg class="w-12 h-12 text-red-400" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
    <p class="text-red-400">{{ error }}</p>
    <button @click="loadLibrary()" class="px-4 py-2 bg-purple-600 rounded-full text-sm hover:bg-purple-500">Tentar novamente</button>
  </div>

  <!-- ── HOME VIEW ── -->
  <div v-else-if="view==='home'" class="p-6">
    <!-- Stats -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold mb-1">Boa <span class="gtext">{{ greeting }}</span> 👋</h1>
      <p class="text-gray-400 text-sm">{{ totalArtists }} artistas · {{ totalAlbums }} álbuns · {{ totalTracks }} músicas</p>
    </div>

    <!-- Empty state -->
    <div v-if="!library?.artists?.length" class="text-center py-16">
      <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3v9.28a4.39 4.39 0 0 0-1.5-.28C8.01 12 6 14.01 6 16.5S8.01 21 10.5 21c2.31 0 4.2-1.75 4.45-4H15V6h4V3h-7z"/></svg>
      <p class="text-gray-400 mb-2">Nenhuma música encontrada</p>
      <p class="text-gray-600 text-sm">Coloque suas músicas em <code class="text-purple-400">music/Artista/Álbum/musica.mp3</code></p>
    </div>

    <!-- Artists grid -->
    <template v-else>
      <div class="mb-8">
        <h2 class="text-xl font-bold mb-4">Artistas</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
          <div v-for="art in library.artists" :key="art.name"
            class="album-card text-center group" @click="gotoArtist(art.name)">
            <div class="w-full aspect-square rounded-full mb-3 overflow-hidden no-cover mx-auto" style="max-width:120px">
              <img v-if="art.albums[0]?.cover" :src="art.albums[0].cover" class="w-full h-full object-cover"/>
              <div v-else class="w-full h-full flex items-center justify-center text-3xl font-black gtext">
                {{ art.name.charAt(0) }}
              </div>
            </div>
            <p class="font-semibold text-sm truncate">{{ art.name }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ art.albums.length }} álbum{{ art.albums.length!==1?'s':'' }}</p>
          </div>
        </div>
      </div>

      <!-- Recent albums -->
      <div>
        <h2 class="text-xl font-bold mb-4">Álbuns</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
          <div v-for="al in allAlbums" :key="al.artist+al.name"
            class="album-card group" @click="gotoAlbum(al.artist,al.name)">
            <div class="aspect-square rounded-lg overflow-hidden mb-3 no-cover">
              <img v-if="al.cover" :src="al.cover" class="w-full h-full object-cover"/>
              <div v-else class="w-full h-full flex items-center justify-center">
                <svg class="w-10 h-10 text-purple-700" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3v9.28a4.39 4.39 0 0 0-1.5-.28C8.01 12 6 14.01 6 16.5S8.01 21 10.5 21c2.31 0 4.2-1.75 4.45-4H15V6h4V3h-7z"/></svg>
              </div>
            </div>
            <p class="font-semibold text-sm truncate">{{ al.name }}</p>
            <p class="text-xs text-gray-400 mt-0.5 truncate">{{ al.artist }}</p>
            <button class="play-btn" @click.stop="playAlbum(al)">
              <svg class="w-5 h-5 text-white ml-0.5" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
            </button>
          </div>
        </div>
      </div>
    </template>
  </div>

  <!-- ── ARTIST VIEW ── -->
  <div v-else-if="view==='artist'&&selectedArtist" class="pb-6">
    <!-- Hero -->
    <div class="relative h-52 flex items-end px-6 pb-6"
      :style="{'--hc': heroColor}" class="hero-gradient">
      <div class="hero-gradient absolute inset-0" :style="{background:`linear-gradient(to bottom, ${heroColor}cc, transparent)`}"></div>
      <div class="relative flex items-end gap-5">
        <div class="w-24 h-24 rounded-full overflow-hidden no-cover flex-shrink-0 shadow-2xl">
          <img v-if="selectedArtist.albums[0]?.cover" :src="selectedArtist.albums[0].cover" class="w-full h-full object-cover"/>
          <div v-else class="w-full h-full flex items-center justify-center text-4xl font-black gtext">{{ selectedArtist.name.charAt(0) }}</div>
        </div>
        <div>
          <p class="text-xs font-semibold text-gray-300 mb-1">Artista</p>
          <h1 class="text-4xl font-black">{{ selectedArtist.name }}</h1>
          <p class="text-gray-300 text-sm mt-1">{{ selectedArtist.albums.length }} álbuns · {{ artistTracks.length }} músicas</p>
        </div>
      </div>
    </div>
    <!-- Controls -->
    <div class="px-6 py-4 flex items-center gap-4">
      <button @click="playAllArtist(selectedArtist)" class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center hover:bg-purple-400 hover:scale-105 transition-all shadow-lg shadow-purple-500/30">
        <svg class="w-6 h-6 text-white ml-1" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
      </button>
      <button @click="shuffleArtist(selectedArtist)" class="text-gray-400 hover:text-white transition-colors">
        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M10.59 9.17L5.41 4 4 5.41l5.17 5.17 1.42-1.41zM14.5 4l2.04 2.04L4 18.59 5.41 20 17.96 7.46 20 9.5V4h-5.5zm.33 9.41l-1.41 1.41 3.13 3.13L14.5 20H20v-5.5l-2.04 2.04-3.13-3.13z"/></svg>
      </button>
    </div>
    <!-- Albums -->
    <div class="px-6">
      <h2 class="text-lg font-bold mb-4">Discografia</h2>
      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
        <div v-for="al in selectedArtist.albums" :key="al.name"
          class="album-card group" @click="gotoAlbum(al.artist,al.name)">
          <div class="aspect-square rounded-lg overflow-hidden mb-3 no-cover">
            <img v-if="al.cover" :src="al.cover" class="w-full h-full object-cover"/>
            <div v-else class="w-full h-full flex items-center justify-center">
              <svg class="w-10 h-10 text-purple-700" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3v9.28a4.39 4.39 0 0 0-1.5-.28C8.01 12 6 14.01 6 16.5S8.01 21 10.5 21c2.31 0 4.2-1.75 4.45-4H15V6h4V3h-7z"/></svg>
            </div>
          </div>
          <p class="font-semibold text-sm truncate">{{ al.name }}</p>
          <p class="text-xs text-gray-400 mt-0.5">{{ al.tracks.length }} faixas</p>
          <button class="play-btn" @click.stop="playAlbum(al)">
            <svg class="w-5 h-5 text-white ml-0.5" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- ── ALBUM VIEW ── -->
  <div v-else-if="view==='album'&&selectedAlbum" class="pb-6">
    <!-- Hero -->
    <div class="relative" :style="{background:`linear-gradient(to bottom, ${heroColor}dd 0%, transparent 100%)`}">
      <div class="flex items-end gap-5 px-6 pt-8 pb-6">
        <div class="w-40 h-40 rounded-xl overflow-hidden no-cover flex-shrink-0 shadow-2xl" style="box-shadow:0 20px 60px rgba(0,0,0,.5)">
          <img v-if="selectedAlbum.cover" :src="selectedAlbum.cover" class="w-full h-full object-cover"/>
          <div v-else class="w-full h-full flex items-center justify-center">
            <svg class="w-16 h-16 text-purple-700" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3v9.28a4.39 4.39 0 0 0-1.5-.28C8.01 12 6 14.01 6 16.5S8.01 21 10.5 21c2.31 0 4.2-1.75 4.45-4H15V6h4V3h-7z"/></svg>
          </div>
        </div>
        <div>
          <p class="text-xs font-semibold text-gray-300 mb-1">Álbum</p>
          <h1 class="text-3xl font-black leading-tight">{{ selectedAlbum.name }}</h1>
          <button @click="gotoArtist(selectedAlbum.artist)" class="text-gray-300 hover:text-white text-sm mt-1 font-semibold transition-colors">{{ selectedAlbum.artist }}</button>
          <p class="text-gray-400 text-sm mt-1">{{ selectedAlbum.tracks.length }} faixas</p>
        </div>
      </div>
    </div>
    <!-- Controls -->
    <div class="px-6 py-3 flex items-center gap-4">
      <button @click="playAlbum(selectedAlbum)" class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center hover:bg-purple-400 hover:scale-105 transition-all shadow-lg shadow-purple-500/30">
        <svg class="w-6 h-6 text-white ml-1" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
      </button>
      <button @click="showAddToPlaylistAlbum(selectedAlbum)" class="text-gray-400 hover:text-white transition-colors" title="Adicionar à playlist">
        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
      </button>
    </div>
    <!-- Track list -->
    <div class="px-4">
      <div v-for="(track,idx) in selectedAlbum.tracks" :key="track.id"
        class="track-row group" :class="{playing:isCurrentTrack(track)}"
        @click="playTrack(track,selectedAlbum.tracks)">
        <!-- Number / wave -->
        <div class="w-8 text-center flex-shrink-0 text-sm text-gray-400">
          <template v-if="isCurrentTrack(track)&&isPlaying">
            <div class="wave mx-auto w-fit"><span></span><span></span><span></span><span></span></div>
          </template>
          <template v-else-if="isCurrentTrack(track)">
            <span class="text-purple-400 font-bold">{{ idx+1 }}</span>
          </template>
          <template v-else>
            <span class="group-hover:hidden">{{ idx+1 }}</span>
            <svg class="hidden group-hover:block w-4 h-4 mx-auto text-white" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
          </template>
        </div>
        <div class="flex-1 min-w-0">
          <p class="truncate text-sm" :class="isCurrentTrack(track)?'text-purple-400 font-semibold':'text-white'">{{ track.title }}</p>
          <p class="truncate text-xs text-gray-400">{{ track.artist }}</p>
        </div>
        <button @click.stop="showAddToPlaylistTrack(track)" class="text-gray-600 hover:text-gray-300 opacity-0 group-hover:opacity-100 transition-all ml-2">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
        </button>
      </div>
    </div>
  </div>

  <!-- ── SEARCH VIEW ── -->
  <div v-else-if="view==='search'" class="p-6">
    <div class="relative mb-6 max-w-lg">
      <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" viewBox="0 0 24 24" fill="currentColor"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
      <input id="search-input" v-model="searchQuery" type="search"
        class="w-full bg-white/10 text-white rounded-full pl-10 pr-4 py-3 outline-none border border-white/10 focus:border-purple-500 text-sm"
        placeholder="Buscar artistas, álbuns, músicas...">
    </div>
    <template v-if="searchQuery.trim()">
      <!-- Artists results -->
      <div v-if="searchResults.artists.length" class="mb-6">
        <h3 class="text-base font-bold mb-3 text-gray-300">Artistas</h3>
        <div class="flex flex-wrap gap-3">
          <button v-for="a in searchResults.artists" :key="a.name" @click="gotoArtist(a.name)"
            class="flex items-center gap-3 bg-white/5 hover:bg-white/10 rounded-full px-4 py-2 transition-colors">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-600 to-pink-500 flex items-center justify-center text-xs font-bold">{{ a.name.charAt(0) }}</div>
            <span class="text-sm font-medium">{{ a.name }}</span>
          </button>
        </div>
      </div>
      <!-- Albums results -->
      <div v-if="searchResults.albums.length" class="mb-6">
        <h3 class="text-base font-bold mb-3 text-gray-300">Álbuns</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3">
          <div v-for="al in searchResults.albums" :key="al.artist+al.name" class="album-card group" @click="gotoAlbum(al.artist,al.name)">
            <div class="aspect-square rounded-lg overflow-hidden mb-2 no-cover">
              <img v-if="al.cover" :src="al.cover" class="w-full h-full object-cover"/>
            </div>
            <p class="text-xs font-semibold truncate">{{ al.name }}</p>
            <p class="text-xs text-gray-400 truncate">{{ al.artist }}</p>
            <button class="play-btn" @click.stop="playAlbum(al)"><svg class="w-4 h-4 text-white ml-0.5" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg></button>
          </div>
        </div>
      </div>
      <!-- Tracks results -->
      <div v-if="searchResults.tracks.length">
        <h3 class="text-base font-bold mb-3 text-gray-300">Músicas</h3>
        <div v-for="track in searchResults.tracks" :key="track.id"
          class="track-row group" :class="{playing:isCurrentTrack(track)}"
          @click="playTrack(track,searchResults.tracks)">
          <div class="w-10 h-10 rounded overflow-hidden no-cover flex-shrink-0">
            <img v-if="track.cover" :src="track.cover" class="w-full h-full object-cover"/>
          </div>
          <div class="flex-1 min-w-0">
            <p class="truncate text-sm" :class="isCurrentTrack(track)?'text-purple-400 font-semibold':''">{{ track.title }}</p>
            <p class="truncate text-xs text-gray-400">{{ track.artist }} — {{ track.album }}</p>
          </div>
          <button @click.stop="showAddToPlaylistTrack(track)" class="text-gray-600 hover:text-gray-300 opacity-0 group-hover:opacity-100">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
          </button>
        </div>
      </div>
      <div v-if="!searchResults.artists.length&&!searchResults.albums.length&&!searchResults.tracks.length" class="text-center py-12 text-gray-500">
        Nenhum resultado para "{{ searchQuery }}"
      </div>
    </template>
    <div v-else class="text-center py-16 text-gray-600">
      <svg class="w-12 h-12 mx-auto mb-3 opacity-50" viewBox="0 0 24 24" fill="currentColor"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
      Digite para buscar
    </div>
  </div>

  <!-- ── PLAYLIST VIEW ── -->
  <div v-else-if="view==='playlist'&&currentPlaylist" class="pb-6">
    <div class="flex items-end gap-5 px-6 pt-8 pb-6" style="background:linear-gradient(to bottom,#1a0a2e,transparent)">
      <div class="w-40 h-40 rounded-xl no-cover flex-shrink-0 shadow-2xl flex items-center justify-center">
        <svg class="w-16 h-16 text-purple-500" viewBox="0 0 24 24" fill="currentColor"><path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z"/></svg>
      </div>
      <div>
        <p class="text-xs font-semibold text-gray-300 mb-1">Playlist</p>
        <h1 class="text-3xl font-black">{{ currentPlaylist.name }}</h1>
        <p class="text-gray-400 text-sm mt-1">{{ currentPlaylist.tracks.length }} músicas</p>
      </div>
    </div>
    <div class="px-6 flex items-center gap-4 mb-4">
      <button @click="playPlaylist(currentPlaylist)" class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center hover:bg-purple-400 hover:scale-105 transition-all shadow-lg shadow-purple-500/30" :disabled="!currentPlaylist.tracks.length">
        <svg class="w-6 h-6 text-white ml-1" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
      </button>
      <button @click="deletePlaylist(currentPlaylist.id)" class="text-gray-500 hover:text-red-400 transition-colors ml-auto" title="Excluir playlist">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
      </button>
    </div>
    <div v-if="!currentPlaylist.tracks.length" class="text-center py-12 text-gray-500 px-6">
      <p>Playlist vazia. Adicione músicas tocando um álbum e clicando em +.</p>
    </div>
    <div class="px-4">
      <div v-for="(track,idx) in currentPlaylist.tracks" :key="track.id"
        class="track-row group" :class="{playing:isCurrentTrack(track)}"
        @click="playTrack(track,currentPlaylist.tracks)">
        <div class="w-8 text-center flex-shrink-0 text-sm text-gray-400">
          <template v-if="isCurrentTrack(track)&&isPlaying">
            <div class="wave mx-auto w-fit"><span></span><span></span><span></span><span></span></div>
          </template>
          <template v-else>
            <span class="group-hover:hidden text-xs">{{ idx+1 }}</span>
            <svg class="hidden group-hover:block w-4 h-4 mx-auto text-white" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
          </template>
        </div>
        <div class="w-10 h-10 rounded overflow-hidden no-cover flex-shrink-0">
          <img v-if="track.cover" :src="track.cover" class="w-full h-full object-cover"/>
        </div>
        <div class="flex-1 min-w-0">
          <p class="truncate text-sm" :class="isCurrentTrack(track)?'text-purple-400 font-semibold':''">{{ track.title }}</p>
          <p class="truncate text-xs text-gray-400">{{ track.artist }} — {{ track.album }}</p>
        </div>
        <button @click.stop="removeFromCurrentPlaylist(track.id)" class="text-gray-600 hover:text-red-400 opacity-0 group-hover:opacity-100 transition-all ml-2">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
        </button>
      </div>
    </div>
  </div>

</main>

<!-- ═══ PLAYER BAR ═══ -->
<div class="area-player player-wrap flex items-center px-3 gap-3">

  <!-- Left: track info -->
  <div class="flex items-center gap-3 flex-1 min-w-0" style="max-width:280px" @click="currentTrack&&(showExpandedPlayer=true)">
    <div class="w-12 h-12 rounded-lg overflow-hidden no-cover flex-shrink-0 cursor-pointer" style="min-width:48px">
      <template v-if="currentTrack?.cover">
        <img :src="currentTrack.cover" class="w-full h-full object-cover" :class="isPlaying?'vinyl-spin':'vinyl-pause'" style="border-radius:50%"/>
      </template>
      <div v-else class="w-full h-full flex items-center justify-center">
        <svg class="w-6 h-6 text-purple-700" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3v9.28a4.39 4.39 0 0 0-1.5-.28C8.01 12 6 14.01 6 16.5S8.01 21 10.5 21c2.31 0 4.2-1.75 4.45-4H15V6h4V3h-7z"/></svg>
      </div>
    </div>
    <div class="min-w-0 cursor-pointer">
      <p class="truncate text-sm font-medium" :class="currentTrack?'text-white':'text-gray-600'">{{ currentTrack?.title || 'Nenhuma música' }}</p>
      <p class="truncate text-xs text-gray-400">{{ currentTrack?.artist || '' }}</p>
    </div>
  </div>

  <!-- Center: controls -->
  <div class="flex-1 flex flex-col items-center gap-1" style="max-width:500px">
    <div class="flex items-center gap-2 md:gap-4">
      <!-- Shuffle -->
      <button @click="shuffle=!shuffle" class="hidden md:block transition-colors" :class="shuffle?'text-purple-400':'text-gray-500 hover:text-white'">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M10.59 9.17L5.41 4 4 5.41l5.17 5.17 1.42-1.41zM14.5 4l2.04 2.04L4 18.59 5.41 20 17.96 7.46 20 9.5V4h-5.5zm.33 9.41l-1.41 1.41 3.13 3.13L14.5 20H20v-5.5l-2.04 2.04-3.13-3.13z"/></svg>
      </button>
      <!-- Prev -->
      <button @click="prevTrack()" class="text-gray-300 hover:text-white transition-colors">
        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M6 6h2v12H6zm3.5 6 8.5 6V6z"/></svg>
      </button>
      <!-- Play/Pause -->
      <button @click="togglePlay()" class="w-10 h-10 bg-white rounded-full flex items-center justify-center hover:scale-105 transition-transform shadow-lg">
        <svg v-if="!isPlaying" class="w-5 h-5 text-black ml-0.5" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
        <svg v-else class="w-5 h-5 text-black" viewBox="0 0 24 24" fill="currentColor"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
      </button>
      <!-- Next -->
      <button @click="nextTrack()" class="text-gray-300 hover:text-white transition-colors">
        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M6 18l8.5-6L6 6v12zM16 6v12h2V6h-2z"/></svg>
      </button>
      <!-- Repeat -->
      <button @click="cycleRepeat()" class="hidden md:block transition-colors relative" :class="repeat!=='none'?'text-purple-400':'text-gray-500 hover:text-white'">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M7 7h10v3l4-4-4-4v3H5v6h2V7zm10 10H7v-3l-4 4 4 4v-3h12v-6h-2v4z"/></svg>
        <span v-if="repeat==='one'" class="absolute -top-1 -right-1 w-3 h-3 bg-purple-400 rounded-full text-black text-[8px] flex items-center justify-center font-bold">1</span>
      </button>
    </div>
    <!-- Progress -->
    <div class="w-full hidden md:flex items-center gap-2 text-xs text-gray-400">
      <span class="w-8 text-right tabular-nums">{{ fmtTime(currentTime) }}</span>
      <div class="prog-wrap flex-1" @click="seekClick">
        <div class="prog-fill" :style="{width:progressPct+'%'}"></div>
        <input type="range" min="0" max="100" step="0.1" :value="progressPct" @input="seekInput" class="absolute inset-0 w-full opacity-0 cursor-pointer" style="height:100%">
      </div>
      <span class="w-8 tabular-nums">{{ fmtTime(duration) }}</span>
    </div>
  </div>

  <!-- Right: volume + queue -->
  <div class="hidden md:flex items-center gap-3 flex-1 justify-end" style="max-width:200px">
    <button @click="showQueue=!showQueue" class="transition-colors" :class="showQueue?'text-purple-400':'text-gray-500 hover:text-white'">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z"/></svg>
    </button>
    <button @click="toggleMute()" class="text-gray-400 hover:text-white transition-colors">
      <svg v-if="isMuted||volume===0" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/></svg>
      <svg v-else class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/></svg>
    </button>
    <input type="range" min="0" max="1" step="0.02" :value="isMuted?0:volume" @input="e=>setVolume(+e.target.value)"
      class="vol w-20" style="accent-color:#a855f7">
  </div>
</div>

<!-- ═══ QUEUE PANEL ═══ -->
<transition name="slide-r">
  <div v-if="showQueue" class="fixed top-0 right-0 bottom-0 w-72 overflow-y-auto" style="background:#111120;border-left:1px solid rgba(255,255,255,.08);z-index:50;padding-bottom:96px">
    <div class="sticky top-0 flex items-center justify-between px-4 py-3" style="background:#111120;border-bottom:1px solid rgba(255,255,255,.06)">
      <span class="font-semibold text-sm">Fila</span>
      <button @click="showQueue=false" class="text-gray-400 hover:text-white"><svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></button>
    </div>
    <div class="p-2">
      <div v-if="!queue.length" class="text-center py-8 text-gray-500 text-sm">Fila vazia</div>
      <div v-for="(t,i) in queue" :key="t.id+'_'+i"
        class="track-row group" :class="{playing:i===queueIndex}"
        @click="jumpToQueue(i)">
        <div class="w-10 h-10 rounded overflow-hidden no-cover flex-shrink-0">
          <img v-if="t.cover" :src="t.cover" class="w-full h-full object-cover"/>
        </div>
        <div class="flex-1 min-w-0">
          <p class="truncate text-xs font-medium" :class="i===queueIndex?'text-purple-400':''">{{ t.title }}</p>
          <p class="truncate text-xs text-gray-500">{{ t.artist }}</p>
        </div>
        <div v-if="i===queueIndex&&isPlaying" class="wave ml-1"><span></span><span></span><span></span></div>
      </div>
    </div>
  </div>
</transition>

<!-- ═══ EXPANDED PLAYER (mobile full screen) ═══ -->
<transition name="slide-up">
  <div v-if="showExpandedPlayer" class="fixed inset-0 flex flex-col" style="background:#09090f;z-index:60">
    <!-- Header -->
    <div class="flex items-center justify-between px-5 pt-10 pb-4">
      <button @click="showExpandedPlayer=false" class="text-gray-400 hover:text-white">
        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M7.41 15.41L12 10.83l4.59 4.58L18 14l-6-6-6 6z"/></svg>
      </button>
      <span class="text-sm font-semibold text-gray-300">Tocando agora</span>
      <div class="w-6"></div>
    </div>
    <!-- Album art -->
    <div class="flex-1 flex items-center justify-center px-10">
      <div class="w-full aspect-square rounded-2xl overflow-hidden no-cover shadow-2xl" style="max-width:320px;box-shadow:0 30px 80px rgba(0,0,0,.7)">
        <img v-if="currentTrack?.cover" :src="currentTrack.cover" class="w-full h-full object-cover" :class="isPlaying?'vinyl-spin':'vinyl-pause'" style="border-radius: 50%"/>
        <div v-else class="w-full h-full flex items-center justify-center">
          <svg class="w-24 h-24 text-purple-700" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3v9.28a4.39 4.39 0 0 0-1.5-.28C8.01 12 6 14.01 6 16.5S8.01 21 10.5 21c2.31 0 4.2-1.75 4.45-4H15V6h4V3h-7z"/></svg>
        </div>
      </div>
    </div>
    <!-- Info + controls -->
    <div class="px-6 pb-12">
      <div class="mb-4">
        <p class="text-xl font-bold truncate">{{ currentTrack?.title || '—' }}</p>
        <p class="text-gray-400 truncate">{{ currentTrack?.artist }}</p>
      </div>
      <!-- Progress -->
      <div class="mb-4">
        <div class="prog-wrap mb-1" @click="seekClick">
          <div class="prog-fill" :style="{width:progressPct+'%'}"></div>
          <input type="range" min="0" max="100" step="0.1" :value="progressPct" @input="seekInput" class="absolute inset-0 w-full opacity-0 cursor-pointer" style="height:100%">
        </div>
        <div class="flex justify-between text-xs text-gray-500 tabular-nums">
          <span>{{ fmtTime(currentTime) }}</span><span>{{ fmtTime(duration) }}</span>
        </div>
      </div>
      <!-- Controls -->
      <div class="flex items-center justify-between">
        <button @click="shuffle=!shuffle" :class="shuffle?'text-purple-400':'text-gray-500'">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M10.59 9.17L5.41 4 4 5.41l5.17 5.17 1.42-1.41zM14.5 4l2.04 2.04L4 18.59 5.41 20 17.96 7.46 20 9.5V4h-5.5zm.33 9.41l-1.41 1.41 3.13 3.13L14.5 20H20v-5.5l-2.04 2.04-3.13-3.13z"/></svg>
        </button>
        <button @click="prevTrack()" class="text-white"><svg class="w-8 h-8" viewBox="0 0 24 24" fill="currentColor"><path d="M6 6h2v12H6zm3.5 6 8.5 6V6z"/></svg></button>
        <button @click="togglePlay()" class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-xl hover:scale-105 transition-transform">
          <svg v-if="!isPlaying" class="w-8 h-8 text-black ml-1" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
          <svg v-else class="w-8 h-8 text-black" viewBox="0 0 24 24" fill="currentColor"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
        </button>
        <button @click="nextTrack()" class="text-white"><svg class="w-8 h-8" viewBox="0 0 24 24" fill="currentColor"><path d="M6 18l8.5-6L6 6v12zM16 6v12h2V6h-2z"/></svg></button>
        <button @click="cycleRepeat()" class="relative" :class="repeat!=='none'?'text-purple-400':'text-gray-500'">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M7 7h10v3l4-4-4-4v3H5v6h2V7zm10 10H7v-3l-4 4 4 4v-3h12v-6h-2v4z"/></svg>
          <span v-if="repeat==='one'" class="absolute -top-1 -right-1 w-3 h-3 bg-purple-400 rounded-full text-black text-[8px] flex items-center justify-center font-bold">1</span>
        </button>
      </div>
      <!-- Volume -->
      <div class="flex items-center gap-3 mt-5">
        <svg class="w-4 h-4 text-gray-500" viewBox="0 0 24 24" fill="currentColor"><path d="M7 9v6h4l5 5V4l-5 5H7z"/></svg>
        <input type="range" min="0" max="1" step="0.02" :value="volume" @input="e=>setVolume(+e.target.value)" class="vol flex-1" style="accent-color:#a855f7">
        <svg class="w-4 h-4 text-gray-500" viewBox="0 0 24 24" fill="currentColor"><path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/></svg>
      </div>
    </div>
  </div>
</transition>

<!-- ═══ ADD TO PLAYLIST MODAL ═══ -->
<transition name="fade">
  <div v-if="showPlaylistModal" class="fixed inset-0 flex items-center justify-center px-4" style="background:rgba(0,0,0,.7);z-index:80" @click.self="showPlaylistModal=false">
    <div class="bg-[#1c1c2e] rounded-2xl p-5 w-full max-w-sm border border-white/10" @click.stop>
      <h3 class="font-bold text-base mb-1">Adicionar à playlist</h3>
      <p class="text-gray-400 text-sm mb-4 truncate">{{ playlistModalTrack?.title }}</p>
      <div class="space-y-1 max-h-48 overflow-y-auto mb-3">
        <button v-for="pl in playlists" :key="pl.id" @click="confirmAddToPlaylist(pl.id)"
          class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10 transition-colors text-sm text-left">
          <svg class="w-4 h-4 text-purple-400 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z"/></svg>
          {{ pl.name }}
        </button>
        <div v-if="!playlists.length" class="text-gray-500 text-sm text-center py-3">Nenhuma playlist criada</div>
      </div>
      <div class="border-t border-white/10 pt-3">
        <div v-if="!showNewPlaylistInput" class="flex gap-2">
          <button @click="showNewPlaylistInput=true" class="flex-1 flex items-center justify-center gap-2 py-2 bg-purple-600 hover:bg-purple-500 rounded-lg text-sm transition-colors">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg> Nova playlist
          </button>
          <button @click="showPlaylistModal=false" class="px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg text-sm transition-colors">Cancelar</button>
        </div>
        <div v-else class="flex gap-2">
          <input v-model="newPlaylistName" @keyup.enter="createAndAdd()" placeholder="Nome da playlist..."
            class="flex-1 bg-white/10 text-white text-sm rounded-lg px-3 py-2 outline-none border border-purple-500/50" autofocus>
          <button @click="createAndAdd()" class="px-3 py-2 bg-purple-600 hover:bg-purple-500 rounded-lg text-sm transition-colors">OK</button>
        </div>
      </div>
    </div>
  </div>
</transition>

</div><!-- /#app -->

<script>
const API_URL = <?= json_encode($apiUrl) ?>;
const { createApp, nextTick } = Vue;

createApp({
  data() {
    return {
      library: null, loading: true, error: null,
      view: 'home',
      selectedArtistName: null, selectedAlbumName: null, selectedPlaylistId: null,
      searchQuery: '',
      audio: null,
      currentTrack: null, queue: [], queueIndex: -1,
      isPlaying: false, currentTime: 0, duration: 0,
      volume: 0.8, isMuted: false,
      shuffle: false, repeat: 'none',
      sidebarOpen: false, showExpandedPlayer: false, showQueue: false,
      playlists: [],
      showPlaylistModal: false, playlistModalTrack: null, playlistModalAlbum: null,
      newPlaylistName: '', showNewPlaylistInput: false,
      creatingPlaylist: false, newPlaylistNameSidebar: '',
      heroColor: '#2a0050',
      bgColors: ['#4a0080','#800040','#003090','#804000'],
      wakeLock: null,
    };
  },

  computed: {
    selectedArtist() {
      return this.library?.artists?.find(a => a.name === this.selectedArtistName) ?? null;
    },
    selectedAlbum() {
      return this.selectedArtist?.albums?.find(a => a.name === this.selectedAlbumName) ?? null;
    },
    currentPlaylist() {
      return this.playlists.find(p => p.id === this.selectedPlaylistId) ?? null;
    },
    allAlbums() {
      return this.library?.artists?.flatMap(a => a.albums) ?? [];
    },
    allTracks() {
      return this.allAlbums.flatMap(a => a.tracks);
    },
    artistTracks() {
      return this.selectedArtist?.albums?.flatMap(a => a.tracks) ?? [];
    },
    progressPct() {
      return this.duration > 0 ? (this.currentTime / this.duration) * 100 : 0;
    },
    searchResults() {
      if (!this.searchQuery.trim() || !this.library) return { artists:[], albums:[], tracks:[] };
      const q = this.searchQuery.toLowerCase();
      return {
        artists: this.library.artists.filter(a => a.name.toLowerCase().includes(q)),
        albums:  this.allAlbums.filter(a => a.name.toLowerCase().includes(q) || a.artist.toLowerCase().includes(q)),
        tracks:  this.allTracks.filter(t => t.title.toLowerCase().includes(q) || t.artist.toLowerCase().includes(q) || t.album.toLowerCase().includes(q)),
      };
    },
    totalArtists() { return this.library?.artists?.length ?? 0; },
    totalAlbums()  { return this.allAlbums.length; },
    totalTracks()  { return this.allTracks.length; },
    greeting() {
      const h = new Date().getHours();
      if (h < 12) return 'manhã';
      if (h < 18) return 'tarde';
      return 'noite';
    },
  },

  watch: {
    currentTrack(t) {
      if (!t) { document.title = 'Rockify'; return; }
      document.title = `${t.title} — ${t.artist} | Rockify`;
      if (t.cover) this.updateBgColors(t.cover);
      if ('mediaSession' in navigator) {
        navigator.mediaSession.metadata = new MediaMetadata({
          title: t.title, artist: t.artist, album: t.album,
          artwork: t.cover ? [{ src: t.cover, sizes:'512x512' }] : [],
        });
      }
    },
    isPlaying(v) { v ? this.acquireWakeLock() : this.dropWakeLock(); },
  },

  async mounted() {
    await this.loadLibrary();
    this.loadPlaylists();

    this.audio = new Audio();
    this.audio.volume = this.volume;

    this.audio.addEventListener('timeupdate', () => { this.currentTime = this.audio.currentTime; });
    this.audio.addEventListener('loadedmetadata', () => { this.duration = this.audio.duration || 0; });
    this.audio.addEventListener('ended', () => this.onEnded());
    this.audio.addEventListener('error', () => { console.warn('audio error'); this.nextTrack(); });

    if ('mediaSession' in navigator) {
      navigator.mediaSession.setActionHandler('play',          () => this.togglePlay());
      navigator.mediaSession.setActionHandler('pause',         () => this.togglePlay());
      navigator.mediaSession.setActionHandler('previoustrack', () => this.prevTrack());
      navigator.mediaSession.setActionHandler('nexttrack',     () => this.nextTrack());
      navigator.mediaSession.setActionHandler('seekto',        (d) => { if (this.audio) this.audio.currentTime = d.seekTime; });
    }

    document.addEventListener('visibilitychange', () => {
      if (document.visibilityState === 'visible' && this.isPlaying) this.acquireWakeLock();
    });

    document.addEventListener('keydown', e => {
      if (e.target.tagName === 'INPUT') return;
      if (e.code === 'Space')       { e.preventDefault(); this.togglePlay(); }
      if (e.code === 'ArrowRight' && e.altKey) { e.preventDefault(); this.nextTrack(); }
      if (e.code === 'ArrowLeft'  && e.altKey) { e.preventDefault(); this.prevTrack(); }
    });
  },

  methods: {
    // ── Library ──────────────────────────────────────
    async loadLibrary() {
      this.loading = true; this.error = null;
      try {
        const r = await fetch(API_URL);
        if (!r.ok) throw new Error(`HTTP ${r.status}`);
        this.library = await r.json();
      } catch(e) {
        this.error = 'Não foi possível carregar a biblioteca musical.';
      } finally {
        this.loading = false;
      }
    },

    // ── Navigation ────────────────────────────────────
    gotoHome()        { this.view='home'; this.sidebarOpen=false; },
    gotoSearch()      { this.view='search'; this.sidebarOpen=false; nextTick(()=>document.getElementById('search-input')?.focus()); },
    gotoArtist(name)  { this.selectedArtistName=name; this.selectedAlbumName=null; this.view='artist'; this.sidebarOpen=false; this.updateHeroFromArtist(name); },
    gotoAlbum(a,al)   { this.selectedArtistName=a; this.selectedAlbumName=al; this.view='album'; this.sidebarOpen=false; this.updateHeroFromAlbum(a,al); },
    gotoPlaylist(id)  { this.selectedPlaylistId=id; this.view='playlist'; this.sidebarOpen=false; },

    updateHeroFromArtist(name) {
      const art = this.library?.artists?.find(a=>a.name===name);
      const cover = art?.albums?.[0]?.cover;
      if (cover) this.extractColor(cover).then(c=>{ this.heroColor=c; });
      else this.heroColor='#2a0050';
    },
    updateHeroFromAlbum(artist,album) {
      const al = this.library?.artists?.find(a=>a.name===artist)?.albums?.find(a=>a.name===album);
      if (al?.cover) this.extractColor(al.cover).then(c=>{ this.heroColor=c; });
      else this.heroColor='#2a0050';
    },

    // ── Player ────────────────────────────────────────
    playTrack(track, contextQueue = null) {
      if (contextQueue) {
        this.queue = [...contextQueue];
        const i = this.queue.findIndex(t=>t.id===track.id);
        this.queueIndex = i>=0 ? i : 0;
      } else {
        const existing = this.queue.findIndex(t=>t.id===track.id);
        if (existing>=0) { this.queueIndex=existing; }
        else { this.queue=[track]; this.queueIndex=0; }
      }
      this.loadAndPlay(this.queue[this.queueIndex]);
    },

    playAlbum(album, startIdx=0) {
      this.queue = [...album.tracks];
      this.queueIndex = startIdx;
      this.loadAndPlay(this.queue[this.queueIndex]);
    },

    playPlaylist(pl, startIdx=0) {
      if (!pl.tracks.length) return;
      this.queue = [...pl.tracks];
      this.queueIndex = startIdx;
      this.loadAndPlay(this.queue[this.queueIndex]);
    },

    playAllArtist(artist) {
      const tracks = artist.albums.flatMap(a=>a.tracks);
      if (!tracks.length) return;
      this.queue = tracks; this.queueIndex = 0;
      this.loadAndPlay(tracks[0]);
    },

    shuffleArtist(artist) {
      const tracks = [...artist.albums.flatMap(a=>a.tracks)];
      for (let i=tracks.length-1;i>0;i--) { const j=Math.floor(Math.random()*(i+1));[tracks[i],tracks[j]]=[tracks[j],tracks[i]]; }
      this.queue = tracks; this.queueIndex = 0;
      this.loadAndPlay(tracks[0]);
    },

    loadAndPlay(track) {
      this.currentTrack = track;
      this.audio.src = track.url;
      this.currentTime = 0; this.duration = 0;
      this.audio.play().then(()=>{ this.isPlaying=true; }).catch(e=>console.warn('play:',e));
    },

    togglePlay() {
      if (!this.currentTrack) {
        const t = this.allTracks[0]; if (t) this.playTrack(t, this.allTracks);
        return;
      }
      if (this.isPlaying) { this.audio.pause(); this.isPlaying=false; }
      else { this.audio.play().then(()=>{ this.isPlaying=true; }); }
    },

    prevTrack() {
      if (this.currentTime>3) { this.audio.currentTime=0; return; }
      let i = this.queueIndex-1;
      if (i<0) { if (this.repeat==='all') i=this.queue.length-1; else { this.audio.currentTime=0; return; } }
      this.queueIndex=i;
      this.loadAndPlay(this.queue[i]);
    },

    nextTrack() {
      if (!this.queue.length) return;
      let i;
      if (this.shuffle) {
        do { i=Math.floor(Math.random()*this.queue.length); } while(i===this.queueIndex&&this.queue.length>1);
      } else {
        i = this.queueIndex+1;
        if (i>=this.queue.length) {
          if (this.repeat==='all') i=0;
          else { this.isPlaying=false; return; }
        }
      }
      this.queueIndex=i;
      this.loadAndPlay(this.queue[i]);
    },

    onEnded() {
      if (this.repeat==='one') { this.audio.currentTime=0; this.audio.play(); }
      else this.nextTrack();
    },

    jumpToQueue(i) { this.queueIndex=i; this.loadAndPlay(this.queue[i]); },

    seekInput(e) { if (this.duration) this.audio.currentTime=(+e.target.value/100)*this.duration; },
    seekClick(e) {
      const r=e.currentTarget.getBoundingClientRect();
      const pct=(e.clientX-r.left)/r.width;
      if (this.duration) this.audio.currentTime=pct*this.duration;
    },

    setVolume(v) { this.volume=v; this.audio.volume=v; if(v>0) this.isMuted=false; },
    toggleMute() { this.isMuted=!this.isMuted; this.audio.muted=this.isMuted; },
    cycleRepeat() { this.repeat={none:'all',all:'one',one:'none'}[this.repeat]; },

    isCurrentTrack(t) { return this.currentTrack?.id===t.id; },

    fmtTime(s) {
      if (!s||isNaN(s)) return '0:00';
      const m=Math.floor(s/60), sec=Math.floor(s%60).toString().padStart(2,'0');
      return `${m}:${sec}`;
    },

    // ── Wake Lock ─────────────────────────────────────
    async acquireWakeLock() {
      if (!('wakeLock' in navigator)||this.wakeLock) return;
      try {
        this.wakeLock = await navigator.wakeLock.request('screen');
        this.wakeLock.addEventListener('release', ()=>{ this.wakeLock=null; if(this.isPlaying) this.acquireWakeLock(); });
      } catch(_) {}
    },
    async dropWakeLock() {
      if (!this.wakeLock) return;
      try { await this.wakeLock.release(); } catch(_) {}
      this.wakeLock=null;
    },

    // ── Color extraction ──────────────────────────────
    extractColor(src) {
      return new Promise(res=>{
        const img=new Image();
        img.onload=()=>{
          const cv=document.createElement('canvas'); cv.width=cv.height=40;
          const ctx=cv.getContext('2d'); ctx.drawImage(img,0,0,40,40);
          const d=ctx.getImageData(0,0,40,40).data;
          let r=0,g=0,b=0,n=0;
          for(let i=0;i<d.length;i+=4){r+=d[i];g+=d[i+1];b+=d[i+2];n++;}
          const f=n||1;
          res(`rgb(${Math.round(r/f*.6)},${Math.round(g/f*.6)},${Math.round(b/f*.6)})`);
        };
        img.onerror=()=>res('#2a0050');
        img.src=src;
      });
    },

    async updateBgColors(src) {
      const img=new Image();
      img.onload=()=>{
        const cv=document.createElement('canvas'); cv.width=cv.height=60;
        const ctx=cv.getContext('2d'); ctx.drawImage(img,0,0,60,60);
        const d=ctx.getImageData(0,0,60,60).data;
        const q=[{r:0,g:0,b:0,n:0},{r:0,g:0,b:0,n:0},{r:0,g:0,b:0,n:0},{r:0,g:0,b:0,n:0}];
        for(let y=0;y<60;y++) for(let x=0;x<60;x++){
          const i=(y*60+x)*4, qi=(y<30?0:2)+(x<30?0:1);
          q[qi].r+=d[i];q[qi].g+=d[i+1];q[qi].b+=d[i+2];q[qi].n++;
        }
        this.bgColors=q.map(({r,g,b,n})=>{
          const f=n||1, mx=Math.max(r/f,g/f,b/f)||1;
          const boost=Math.min(2.5,100/mx);
          return `rgb(${Math.min(255,Math.round(r/f*boost*.5))},${Math.min(255,Math.round(g/f*boost*.5))},${Math.min(255,Math.round(b/f*boost*.5))})`;
        });
      };
      img.src=src;
    },

    // ── Playlists ─────────────────────────────────────
    loadPlaylists() {
      try { this.playlists=JSON.parse(localStorage.getItem('rockify_pls')||'[]'); } catch(_){ this.playlists=[]; }
    },
    savePlaylists() { localStorage.setItem('rockify_pls',JSON.stringify(this.playlists)); },

    startCreatePlaylist() { this.creatingPlaylist=true; nextTick(()=>this.$refs.plInput?.focus()); },
    confirmCreatePlaylist() {
      const name=(this.newPlaylistNameSidebar||this.newPlaylistName||'Nova Playlist').trim();
      const pl={id:Date.now().toString(),name,tracks:[],createdAt:Date.now()};
      this.playlists.push(pl); this.savePlaylists();
      this.creatingPlaylist=false; this.newPlaylistNameSidebar=''; this.newPlaylistName='';
    },

    deletePlaylist(id) {
      this.playlists=this.playlists.filter(p=>p.id!==id); this.savePlaylists();
      if(this.view==='playlist'&&this.selectedPlaylistId===id) this.view='home';
    },

    showAddToPlaylistTrack(track) { this.playlistModalTrack=track; this.playlistModalAlbum=null; this.showPlaylistModal=true; this.showNewPlaylistInput=false; },
    showAddToPlaylistAlbum(album) { this.playlistModalAlbum=album; this.playlistModalTrack=null; this.showPlaylistModal=true; this.showNewPlaylistInput=false; },

    confirmAddToPlaylist(plId) {
      const pl=this.playlists.find(p=>p.id===plId); if(!pl) return;
      if(this.playlistModalTrack) {
        if(!pl.tracks.find(t=>t.id===this.playlistModalTrack.id)) pl.tracks.push(this.playlistModalTrack);
      } else if(this.playlistModalAlbum) {
        this.playlistModalAlbum.tracks.forEach(t=>{ if(!pl.tracks.find(x=>x.id===t.id)) pl.tracks.push(t); });
      }
      this.savePlaylists(); this.showPlaylistModal=false;
    },

    createAndAdd() {
      if(!this.newPlaylistName.trim()) return;
      const pl={id:Date.now().toString(),name:this.newPlaylistName.trim(),tracks:[],createdAt:Date.now()};
      this.playlists.push(pl); this.savePlaylists();
      this.confirmAddToPlaylist(pl.id);
      this.newPlaylistName=''; this.showNewPlaylistInput=false;
    },

    removeFromCurrentPlaylist(trackId) {
      const pl=this.playlists.find(p=>p.id===this.selectedPlaylistId); if(!pl) return;
      pl.tracks=pl.tracks.filter(t=>t.id!==trackId); this.savePlaylists();
    },
  },
}).mount('#app');
</script>
</body>
</html>
