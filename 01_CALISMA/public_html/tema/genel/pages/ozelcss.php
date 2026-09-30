<style>
.modern-baskan-section {
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
  min-height: 80vh;
  display: flex;
  align-items: center;
  position: relative;
  overflow: hidden;
}

.modern-baskan-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background:
    radial-gradient(circle at 20% 20%, rgba(59, 130, 246, 0.1) 0%, transparent 50%),
    radial-gradient(circle at 80% 80%, rgba(16, 185, 129, 0.1) 0%, transparent 50%);
  z-index: 1;
}

.baskan-image-side, .baskan-content-side {
  position: relative;
  z-index: 2;
}

/* Fotoğraf Bölümü */
.baskan-image-wrapper {
  position: relative;
  height: 100%;
  min-height: 600px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  padding: 3rem;
}

.baskan-image-container {
  position: relative;
  max-width: 490px;
  margin-bottom: 2rem;
}

.baskan-main-image {
  width: 100%;
  height: auto;
  border-radius: 20px;
  box-shadow:
    0 25px 50px -12px rgba(0, 0, 0, 0.25),
    0 0 0 1px rgba(255, 255, 255, 0.05);
  transition: transform 0.3s ease;
}

.baskan-main-image:hover {
  transform: translateY(-5px);
}

/* Dekoratif Elementler */
.baskan-decor-1 {
  position: absolute;
  top: -20px;
  right: -20px;
  width: 100px;
  height: 100px;
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  border-radius: 50%;
  opacity: 0.1;
  z-index: -1;
}

.baskan-decor-2 {
  position: absolute;
  bottom: -30px;
  left: -30px;
  width: 150px;
  height: 150px;
  background: linear-gradient(135deg, #10b981, #059669);
  border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
  opacity: 0.1;
  z-index: -1;
}

/* Quote Box */
.baskan-quote-box {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border-radius: 20px;
  padding: 2rem;
  max-width: 350px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.quote-icon {
  color: #3b82f6;
  font-size: 2rem;
  margin-bottom: 1rem;
}

.baskan-quote {
  font-size: 1.1rem;
  line-height: 1.6;
  color: #374151;
  font-style: italic;
  margin: 0 0 1rem 0;
}

.quote-author {
  font-weight: 600;
  color: #1f2937;
  text-align: right;
}

/* İçerik Bölümü */
.baskan-content-side {
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(249, 250, 251, 0.9) 100%);
}

.baskan-content-wrapper {
  padding: 4rem 3rem;
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.baskan-header {
  margin-bottom: 2rem;
}

.baskan-subtitle {
  display: inline-block;
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  margin-bottom: 1rem;
}

.baskan-name {
  font-size: 3rem;
  font-weight: 800;
  color: #1f2937;
  margin: 1rem 0;
  line-height: 1.1;
}

.baskan-title-line {
  width: 80px;
  height: 4px;
  background: linear-gradient(135deg, #10b981, #059669);
  border-radius: 2px;
}

.baskan-about {
  margin: 2rem 0;
}

.baskan-description {
  font-size: 1.1rem;
  line-height: 1.8;
  color: #6b7280;
  margin: 0;
}

/* İstatistik Kartları */
.baskan-stats {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
  margin: 2rem 0;
}

.stat-card {
  text-align: center;
  padding: 1.5rem;
  background: rgba(255, 255, 255, 0.8);
  border-radius: 15px;
  border: 1px solid rgba(255, 255, 255, 0.3);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.stat-number {
  font-size: 2rem;
  font-weight: 800;
  color: #3b82f6;
  margin-bottom: 0.5rem;
}

.stat-label {
  font-size: 0.9rem;
  color: #6b7280;
  font-weight: 500;
}

/* İletişim Bölümü */
.baskan-contact {
  margin-top: 2rem;
}

.social-links {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
}

.social-link {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  text-decoration: none;
  transition: all 0.3s ease;
  font-size: 1.2rem;
}

.social-link:hover {
  transform: translateY(-3px) scale(1.1);
  text-decoration: none;
  color: white;
}

.social-link.facebook { background: linear-gradient(135deg, #1877f2, #0d5bb5); }
.social-link.twitter { background: linear-gradient(135deg, #1da1f2, #0d8bd9); }
.social-link.instagram { background: linear-gradient(135deg, #e4405f, #833ab4); }
.social-link.linkedin { background: linear-gradient(135deg, #0077b5, #005885); }
.social-link.youtube { background: linear-gradient(135deg, #ff0000, #cc0000); }

.modern-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
  padding: 1rem 2rem;
  border-radius: 25px;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s ease;
  box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
}

.modern-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 15px 35px rgba(16, 185, 129, 0.4);
  text-decoration: none;
  color: white;
}

.modern-btn i {
  transition: transform 0.3s ease;
}

.modern-btn:hover i {
  transform: translateX(5px);
}

/* Responsive */
@media (max-width: 991px) {
  .modern-baskan-section {
    min-height: auto;
  }

  .baskan-image-wrapper {
    min-height: 400px;
    padding: 2rem;
  }

  .baskan-content-wrapper {
    padding: 3rem 2rem;
  }

  .baskan-name {
    font-size: 2.5rem;
  }

  .baskan-stats {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 576px) {
  .baskan-image-wrapper {
    min-height: 300px;
    padding: 1.5rem;
  }

  .baskan-content-wrapper {
    padding: 2rem 1.5rem;
  }

  .baskan-name {
    font-size: 2rem;
  }

  .baskan-stats {
    grid-template-columns: 1fr;
    gap: 1rem;
  }

  .social-links {
    justify-content: center;
  }

  .modern-btn {
    width: 100%;
    justify-content: center;
  }
}

/* Bant ve üst çizgi */
.quickmenu-band{position:relative;padding:42px 0 20px;background:#fff}
.quickmenu-topline{height:10px;background:#0aa0a9}

/* Carousel kapsayıcı */
.owl-carousel-hizlimenu .item{padding:6px}

/* Kart kutusu */
.hizli-menu-box{position:relative;height:100%}

/* Kartın kendisi */
.qm-card{
  display:flex; flex-direction:column; justify-content:center; gap:8px;
    height: 14pc;
  padding:18px 18px 64px; /* altta buton için alan */
  border-radius:18px; box-shadow:0 16px 34px rgba(0,0,0,.10);
  color:#083942; text-decoration:none !important; position:relative;
  transition:transform .16s ease, box-shadow .16s ease, filter .16s ease;
  overflow:hidden;
}

.qm-card::after{ /* parlaklık/texture */
  content:""; position:absolute; inset:0; pointer-events:none; opacity:.15;
  background:
    radial-gradient(130px 90px at 20% 20%, rgba(255,255,255,.9), transparent 60%),
    radial-gradient(160px 110px at 80% 35%, rgba(255,255,255,.55), transparent 70%);
}
.qm-card:hover{transform:translateY(-4px);box-shadow:0 22px 40px rgba(0,0,0,.14);filter:saturate(1.03)}

/* Simge rozeti */
.hizli-icon{
  width:48px;height:48px;border-radius:14px;display:grid;place-items:center;
  background:rgba(255,255,255,.85);box-shadow:inset 0 0 0 2px rgba(255,255,255,.5);
}
.hizli-icon i{font-size:26px;line-height:1}

/* Başlık ve alt yazı */
.qm-title{    color: white;font:800 18px/1.15 "Montserrat",sans-serif;margin:4px 0 0}
.qm-sub {
    margin: 4px 0 0;
    font-weight: 600;
    opacity: .95;
    color: #f3f3f3;
}
/* Alttaki yarı saydam buton (mevcut <a class="hizli-back">) */
.hizli-back{
  position:absolute;left:12px;right:12px;bottom:12px;height:44px;
  display:flex;align-items:center;justify-content:center;
  border-radius:12px;color:#083942;text-decoration:none !important;
  transform:translateY(6px);opacity:0;transition:.18s ease;
  box-shadow:0 8px 18px rgba(0,0,0,.10); font-weight:800;
}
.hizli-menu-box:hover .hizli-back{transform:translateY(0);opacity:1}

/* Linklerde alt-çizgiyi global kapat */
.hizli-menu-box a,
.hizli-menu-box a:hover{ text-decoration:none !important }

/* Özel Owl nav (custom-owl-nav.hizlimenu-nav içinde) */
.hizlimenu-nav{
  display:flex;gap:10px;justify-content:flex-end;margin-bottom:10px
}
.hizlimenu-nav .owl-prev, .hizlimenu-nav .owl-next{
  width:40px;height:40px;border-radius:50%;display:inline-grid;place-items:center;
  background:#0aa0a9;color:#fff;font-weight:900;font-size:18px;
  box-shadow:0 10px 20px rgba(10,160,169,.25);transition:.15s ease
}
.hizlimenu-nav .owl-prev:hover, .hizlimenu-nav .owl-next:hover{filter:brightness(1.08)}
.hizlimenu-nav .owl-prev[disabled], .hizlimenu-nav .owl-next[disabled]{opacity:.4}

/* Responsive */
@media (max-width:1200px){ .qm-card{min-height:160px} }
@media (max-width:992px){
  .hizlimenu-nav{justify-content:center;margin-bottom:14px}
.qm-card {
    min-height: 177px;
    height: 14pc;
}
}
@media (max-width:600px){
  .qm-title{font-size:16px}
  .qm-sub{font-size:14px}
}

/* ========== CONNECT WITH US (Haberler) ========== */
.social .wrap{max-width:1180px;margin:0 auto;padding:0 24px}
.social h4{font:900 28px/1.1 "Montserrat",sans-serif;margin:20px 0 18px;color:#0a434a}

/* grid + card */
.social .feed{display:grid;grid-template-columns:repeat(3,1fr);gap:22px}
.post{border:1px solid #eaf0f2;border-radius:18px;overflow:hidden;background:#fff;
      box-shadow:0 10px 30px rgba(0,0,0,.08);transition:transform .15s, box-shadow .15s}
.post:hover{transform:translateY(-2px);box-shadow:0 16px 34px rgba(0,0,0,.10)}
/* görsel oranı (lazy çalışmasa da alan sabit): */
.post img{display:block;width:100%;height:auto;object-fit:cover;aspect-ratio:16/9;min-height:220px}
@supports not (aspect-ratio: 1){ .post img{height:260px} }

.post .txt{padding:12px 14px;color:#4b6368}
.post .txt small{display:block;opacity:.75}
.post .txt .title{font-weight:800;color:#0a434a;margin:4px 0 6px;
                  display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.post .txt p{margin:0;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}

/* underline tamamen kapalı */
.social a,.social a:hover,.social a *{text-decoration:none !important}

/* “All News” butonu */
.social-more{margin-top:18px;text-align:center}
.btn-social{display:inline-block;padding:10px 18px;border-radius:999px;background:#d9ea56;color:#123;font-weight:800;text-decoration:none}
.btn-social:hover{filter:brightness(1.05)}

/* responsive */
@media (max-width:1200px){ .social .feed{grid-template-columns:repeat(3,1fr)} }
@media (max-width:992px){ .social .feed{grid-template-columns:repeat(2,1fr)} }
@media (max-width:640px){  .social .feed{grid-template-columns:1fr} }

/* ========== ETKİNLİK (JA stili) ========== */
.events-band{position:relative;padding:70px 0;background:linear-gradient(180deg,#e0f3f6 0,#cfeaf0 60%,#c5e4ec 100%)}
.events-topline{position:absolute;left:0;right:0;top:0;height:10px;background:#0aa0a9}
.evt-grid{row-gap:24px}

/* kart yapısı */
.evt-card{background:#fff;border:1px solid #e7eff2;border-radius:24px;box-shadow:0 24px 50px rgba(0,0,0,.08);padding:18px}
.evt-head{display:flex;justify-content:space-between;align-items:center;padding:4px 6px 10px}
.evt-head h5{margin:0;font:800 16px/1.2 "Montserrat",sans-serif;color:#0a434a}
.evt-link {
    font-weight: 600;
    background: #F59C1C;
    padding: 8px 12px;
    border-radius: 999px;
    color: #ffffff;
}
.evt-link:hover{filter:brightness(1.05);color:white;}
.events-band a,.events-band a:hover{text-decoration:none}

/* sekmeler */
.evt-tabs{display:flex;gap:8px;background:#f1fbfb;border:1px solid #e1f1f3;padding:6px;border-radius:16px;margin-bottom:10px}
.evt-tabs .tab{appearance:none;border:0;background:transparent;padding:8px 12px;border-radius:12px;font-weight:800;color:#0a4b53;cursor:pointer;transition:all 0.3s ease}
.evt-tabs .tab.active{background:#0aa0a9;color:#fff}
.evt-tabs .tab:hover{background:rgba(10,160,169,0.1)}

/* paneller + satır listesi */
.pane{display:none}
.pane.show{display:block}
.evt-list{list-style:none;margin:0;padding:0}
.evt-list .item + .item{border-top:1px dashed #e6eef0}
.evt-list .item a{display:flex;justify-content:space-between;gap:12px;padding:12px 4px;color:#0a434a;transition:all 0.3s ease}
.evt-list .item a:hover{background:#f8f9fa;padding-left:12px}
.evt-list .title{font-weight:700}
.evt-list .meta{opacity:.8;white-space:nowrap}

/* yaklaşan etkinlik kartı */
.evt-upcoming{display:grid;grid-template-columns:120px 1fr;gap:14px;border:1px solid #edf3f5;border-radius:16px;overflow:hidden;margin:10px 0 16px}
.evt-upcoming .thumb img{display:block;width:100%;height:auto;aspect-ratio:1/1;min-height:120px;object-fit:cover}
.evt-upcoming .meta{padding:10px}
.evt-upcoming .ttl{margin:0 0 6px;font-weight:800;color:#0a434a}
.evt-upcoming ul{list-style:none;margin:0 0 8px;padding:0;display:flex;flex-wrap:wrap;gap:10px;color:#47636a}
.evt-cta{display:inline-block;font-weight:800;background:#0aa0a9;color:#fff;padding:8px 12px;border-radius:10px}

/* geçmiş etkinlikler */
.evt-subhead{margin:14px 0 8px;font-weight:800;color:#0a434a}
.evt-past{display:flex;gap:10px;align-items:flex-start;padding:10px 0;border-top:1px dashed #e6eef0;color:#0a434a}
.evt-past .ttl{font-weight:700}
.evt-past .date{opacity:.75}

/* başkanla foto – carousel görselleri güvenli alan */
.evt-gallery .item{position:relative}
.evt-gallery .item::before{content:"";display:block;padding-top:56.25%} /* 16:9 placeholder */
.evt-gallery .item img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;border-radius:14px}
.owl-carousel-etkinlik .owl-stage-outer{min-height:240px}

/* Başkan Galeri Navigation */
.baskan-galeri-nav{
  display:flex;
  gap:10px;
  justify-content:flex-end;
  margin-bottom:10px;
  padding:0 10px;
}
.baskan-galeri-nav .owl-prev, 
.baskan-galeri-nav .owl-next{
  width:40px;
  height:40px;
  border-radius:50%;
  display:inline-grid;
  place-items:center;
  background:#0aa0a9;
  color:#fff;
  font-weight:900;
  font-size:18px;
  box-shadow:0 10px 20px rgba(10,160,169,.25);
  transition:.15s ease;
  cursor:pointer;
  border:none;
}
.baskan-galeri-nav .owl-prev:hover, 
.baskan-galeri-nav .owl-next:hover{
  filter:brightness(1.08);
  transform:scale(1.05);
}
.baskan-galeri-nav .owl-prev.disabled, 
.baskan-galeri-nav .owl-next.disabled{
  opacity:.4;
  cursor:not-allowed;
}

/* responsive */
@media (max-width:1024px){ 
  .evt-upcoming{grid-template-columns:1fr}
  .baskan-galeri-nav{justify-content:center}
}


/* container helper (tema’da yoksa) */
.social .wrap{max-width:1180px;margin:0 auto;padding:0 24px} 

/* grid */
.social .feed{display:grid;grid-template-columns:repeat(4,1fr);gap:22px}

/* card */
.post{border:1px solid #eaf0f2;border-radius:18px;overflow:hidden;background:#fff;
      box-shadow:0 10px 30px rgba(0,0,0,.08);transition:transform .15s, box-shadow .15s}
.post img{display:block;width:100%;height:225px;object-fit:cover}
.post .txt{padding:12px 14px;color:#4b6368}
.post .date{display:block;opacity:.75}
.post .title{font-weight:800;color:#0a434a;margin:4px 0 6px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.post .excerpt{margin:0;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.post:hover{transform:translateY(-2px);box-shadow:0 16px 34px rgba(0,0,0,.10)}

/* underline fix (temadaki global kuralları kesin olarak geçersiz kılar) */
.social a,
.social a:hover,
.social a *{text-decoration:none !important}

/* “All News” butonu */
.social-more{margin-top:18px;text-align:center}
.btn-social{display:inline-block;padding:10px 18px;border-radius:999px;background:#d9ea56;
            color:#123;font-weight:800;text-decoration:none}
.btn-social:hover{filter:brightness(1.05)}

/* responsive kırılımlar */
@media (max-width:1200px){ .social .feed{grid-template-columns:repeat(3,1fr)} }
@media (max-width:992px){ .social .feed{grid-template-columns:repeat(2,1fr)} }
@media (max-width:640px){  .social .feed{grid-template-columns:1fr} }


.programs-section {
	background: #fff;
	padding: 120px 0;
	position: relative;
}

.programs-section::before {
	content: "";
	position: absolute;
	inset: 0 0 auto 0;
	height: 100%;
	background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="280" height="220" viewBox="0 0 280 220"><defs><pattern id="g" width="140" height="110" patternUnits="userSpaceOnUse"><path d="M0 110L70 0L140 110Z" fill="none" stroke="%238cc3b0" stroke-opacity="0.35"/></pattern></defs><rect width="100%" height="100%" fill="url(%23g)"/></svg>');
	opacity: .45;
	background-size: 520px auto;
	background-position: 8% 0;
	pointer-events: none;
}

.programs-grid {
	display: grid;
	grid-template-columns: 1.1fr 1fr;
	gap: 30px;
	align-items: start;
}

.programs-content .pill {
	display: inline-block;
	background: #fff;
	border: 2px solid #e1f0f2;
	border-radius: 999px;
	padding: 6px 12px;
	font-weight: 800;
	color: #f51c1c
	font-size: 12px;
	letter-spacing: .08em;
	margin-bottom: 12px;
}

.programs-title {
	font: 900 42px/1.05 Montserrat, Arial, sans-serif;
	margin: .2rem 0 1rem; 
	color: #093a43;
}

.programs-description {
	color: #48646a;
	font-size: 16px;
	line-height: 1.6;
	margin-bottom: 16px;
}

.callout {
    margin-top: 16px;
    border-left: 4px solid #000000;
    padding-left: 14px;
    font-weight: 800;
    color: #f51c1c;
    display: inline-block;
    text-decoration: none;
}

.callout:hover {
	text-decoration: underline;
}

.program-cards {
	display: grid;
	grid-template-columns: repeat(2, 1fr);
	gap: 22px;
}

.p-card {
	background: #fff;
	border-radius: 20px;
	box-shadow: 0 30px 35px rgba(0,0,0,.08);
	padding: 14px;
	border: 1px solid #eef2f4;
	transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.p-card:hover {
	transform: translateY(-5px);
	box-shadow: 0 35px 40px rgba(0,0,0,.12);
}

.p-card img {
	height: 160px;
	width: 100%;
	object-fit: cover;
	border-radius: 14px;
}

.p-card b {
	display: block;
	margin: 10px 6px 6px;
	color: #083942;
	text-transform: uppercase;
	letter-spacing: .03em;
	font-weight: 700;
	font-size: 14px;
}

.quote-wrap {
	display: flex;
	justify-content: center;
	margin-top: 60px;
}

.quote {
	position: relative;
	background: #bfe8ec;
	padding: 30px 36px;
	border-radius: 12px;
	max-width: 820px;
	text-align: center;
	font-style: italic;
	color: #093a43;
	font-size: 18px;
	line-height: 1.6;
}

.quote::before,
.quote::after {
	content: "";
	position: absolute;
	top: 50%;
	width: 0;
	height: 0;
	border-top: 12px solid transparent;
	border-bottom: 12px solid transparent;
}

.quote::before {
	left: -12px;
	border-right: 12px solid #94d7de;
}

.quote::after {
	right: -12px;
	border-left: 12px solid #94d7de;
}

/* Responsive styles */
@media (max-width: 1024px) {
	.programs-grid {
		grid-template-columns: 1fr;
	}
}

@media (max-width: 768px) {
	.programs-section {
		padding: 80px 0;
	}

	.programs-title {
		font-size: 36px;
	}

	.program-cards {
		grid-template-columns: 1fr;
	}

	.quote {
		font-size: 16px;
		padding: 20px;
	}
}

@media (max-width: 576px) {
	.programs-section {
		padding: 60px 0;
	}

	.programs-title {
		font-size: 32px;
	}

	.programs-description {
		font-size: 14px;
	}
}
@keyframes bg-button {
    0% {
        background-position: 100% 0
    }

    to {
        background-position: 0 0
    }
}

.impact-reach-wrapper {
    opacity: 1;
    position: relative
}

.impact-reach-container {
    align-items: flex-start;
    display: flex;
    flex-direction: column;
    gap: 30px
}

@media (min-width: 1200px) {
    .impact-reach-container {
        flex-direction:row
    }

    .impact-reach-sol {
        width: 419px
    }
}

.impact-reach-sol .impact-icon-wrapper {
    display: block;
    height: 123px;
    margin-bottom: 70px
}

.impact-reach-sol .impact-icon-img {
    -o-object-fit: contain;
    object-fit: contain;
    -o-object-position: top;
    object-position: top
}

.impact-reach-sol .impact-baslik {
    margin-bottom: 14px!important;
    font-size: 3.2rem;
    font-weight: 700;
    color: #f51c1c !important;
    line-height: 1.1;
    font-family: 'Arial', sans-serif;
    letter-spacing: -0.5px;
}
h1, h2, h3, h4, h5, h6, .g-title, .section-title, .panel-title {
    font-family: var(--font-heading) !important;
    font-weight: 700 !important;
    letter-spacing: 0.5px;
    color: #f51c1c !important;
}
.impact-reach-sol .impact-aciklama {
    font-size: 1.15rem;
    color: #666666;
    line-height: 1.6;
    font-family: 'Arial', sans-serif;
}

.impact-reach-sag {
    display: flex;
    flex: 1;
    flex-direction: column;
    gap: 30px;
    padding-left: 20px;
    padding-top: 40px
}

@media (min-width: 576px) {
    .impact-reach-sag {
        display:grid;
        grid-template-columns: 1fr 1fr;
        padding-left: 0
    }
}

@media (min-width: 1200px) {
    .impact-reach-sag {
        padding-top:134px
    }
}

.impact-card-bilgi {
    padding: 30px 27px 28px;
    position: relative;
    border-radius: 8px;
    transition: transform 0.3s ease;
}

.impact-card-bilgi:hover {
    transform: translateY(-3px);
}

/* Arkaplan Renkleri - Grid'de paralel dizilim için */
/* Sol sütun (1., 3., 5. kartlar) - Mavi */
.impact-reach-sag .impact-card-bilgi:nth-child(odd) {
    background-color: #C9EBF2 !important;
}

/* Sağ sütun (2., 4., 6. kartlar) - Sarı */
.impact-reach-sag .impact-card-bilgi:nth-child(even) {
    background-color: #DFF29C !important;
}

/* Yazı ve Sayı Renkleri */
.impact-card-bilgi .card-sayi {
    color: #2C5F5F;
    font-weight: 700;
    font-size: 2.4rem;
    font-family: 'Arial', sans-serif;
    letter-spacing: -0.5px;
    margin-bottom: 0;
}

.impact-card-bilgi .card-baslik {
    color: #2C5F5F;
    font-weight: 500;
    font-family: 'Arial', sans-serif;
}

@media (min-width: 576px) {
    .impact-card-bilgi {
        padding:35px 30px 28px
    }
}

.impact-card-bilgi:first-child {
    order: 1;
    padding-top: 72px
}

.impact-card-bilgi:nth-child(3) {
    order: 2
}

.impact-card-bilgi:nth-child(4) {
    order: 5
}

@media (min-width: 576px) {
    .impact-card-bilgi:first-child,.impact-card-bilgi:nth-child(2),.impact-card-bilgi:nth-child(3),.impact-card-bilgi:nth-child(4) {
        order:unset
    }

    .impact-card-bilgi:first-child {
        padding-top:85px
    }
}

.impact-card-bilgi .card-baslik {
    font-weight: 700
}

.impact-card-bilgi .card-sayi {
    margin-bottom: 0
}

.impact-card-bilgi .card-arrow-wrapper {
    left: 0;
    position: absolute;
    top: 10px;
    transform: translateX(-55%)
}

/* Mobile Responsive */
@media (max-width: 1200px) {
    .impact-reach-sol .impact-baslik {
        font-size: 2.8rem;
    }

    .impact-reach-sol .impact-aciklama {
        font-size: 1.05rem;
    }

    .impact-card-bilgi .card-sayi {
        font-size: 2rem;
    }
}

@media (max-width: 992px) {
    .impact-by-numbers-section {
        padding: 80px 0 !important;
    }

    .impact-reach-container {
        gap: 40px;
    }

    .impact-reach-sol {
        width: 100% !important;
        text-align: center;
    }

    .impact-reach-sol .impact-baslik {
        font-size: 2.4rem;
    }

    .impact-reach-sol .impact-aciklama {
        font-size: 1rem;
    }

    .impact-reach-sag {
        width: 100%;
        padding-left: 0 !important;
        padding-top: 0 !important;
    }
}

@media (max-width: 768px) {
    .impact-by-numbers-section {
        padding: 60px 0 !important;
    }

    .impact-reach-sol .impact-baslik {
        font-size: 2rem;
    }

    .impact-card-bilgi .card-sayi {
        font-size: 1.8rem;
    }

    .impact-card-bilgi .card-baslik {
        font-size: 0.9rem;
    }
}

@media (max-width: 576px) {
    .impact-by-numbers-section {
        padding: 40px 0 !important;
    }

    .impact-reach-container {
        gap: 30px;
    }

    .impact-reach-sol .impact-baslik {
        font-size: 1.8rem;
    }

    .impact-reach-sol .impact-aciklama {
        font-size: 0.95rem;
    }

    .impact-reach-sag {
        flex-direction: column !important;
        gap: 20px;
    }

    .impact-card-bilgi {
        width: 100%;
        padding: 25px 20px 20px;
    }

    .impact-card-bilgi:first-child {
        padding-top: 60px;
    }

    .impact-card-bilgi .card-sayi {
        font-size: 1.6rem;
    }

    .impact-card-bilgi .card-baslik {
        font-size: 0.85rem;
    }
}

/* ====== Shared ====== */
.gallery-band, .contact-band{position:relative;padding:60px 0}
.gallery-band .topline, .contact-topline{position:absolute;left:0;right:0;top:0;height:10px;background:#0aa0a9}
.gallery-band .container, .contact-band .contact-us{position:relative;z-index:2}
.gallery-band a, .contact-band a{ text-decoration:none }

/* Arka plan görseli tam kaplasın (inline <img> ile) */
.bg-festival{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;z-index:1;filter:brightness(.96)}
/* panel (sol/sağ açıklama kartı) */
.gallery-description.panel{position:relative;z-index:3;background:linear-gradient(135deg,rgba(248,255,235,.92),rgba(224,245,243,.92));
  border:1px solid #e7eee7;border-radius:24px;padding:22px;box-shadow:0 14px 40px rgba(0,0,0,.12);display:flex;flex-direction:column;gap:10px;width:100%}
.gallery-description.panel.alt{background:linear-gradient(135deg,rgba(224,245,243,.92),rgba(248,255,235,.92))}
.gallery-description .title{font:900 22px/1.2 "Montserrat",sans-serif;color:#0b4552;text-transform:uppercase;letter-spacing:.02em}
.gallery-description .text{color:#47636a}
.button-border.light{display:inline-flex;align-items:center;gap:8px;padding:10px 16px;border-radius:999px;background:#d9ea56;color:#123;font-weight:800;border:0}
.button-border.light .icon{display:inline-grid;place-items:center}

/* ====== Gallery cards (Video+Foto) ====== */
.gallery-card{background:#fff;border:1px solid #eaf0f2;border-radius:18px;overflow:hidden;box-shadow:0 12px 30px rgba(0,0,0,.10)}
.gallery-card .gallery-cover{position:relative;height: 230px !important;}
.gallery-card .gallery-cover::before{content:"";display:block;padding-top:56.25%} /* 16:9 oran */
.gallery-card .gallery-cover img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}
.gallery-card .play-badge, .gallery-card .img-badge {
    position: absolute;
    left: 5px;
    bottom: 180px;
    display: inline-grid;
    place-items: center;
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: #ff0033;
    color: #fff;
    box-shadow: 0 8px 20px rgba(0,0,0,.18);
}
.gallery-card .img-badge{background:#0aa0a9}
.gallery-card .gallery-body{padding:12px 14px}
.gallery-card .title {
    font-weight: 800;
    color: #ffffff;
    margin: 0 0 6px;
    opacity: 1;
    font-size: 14px;
}
.gallery-card .date{    color: #ffffff;    opacity: 1;display:flex;align-items:center;gap:8px}
.clamp-2{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}

/* Owl eleman aralıkları tutarlı olsun */
.owl-carousel-videogaleri .col-12, .owl-carousel-fotogaleri .col-12{padding:6px}

/* ====== Contact ====== */
.card-like{background:#fff;border:1px solid #e7eff2;border-radius:24px;box-shadow:0 24px 50px rgba(0,0,0,.08);padding:22px}
.contact-us-content .small-title{display:inline-block;background:#e2f4f0;border-radius:999px;padding:6px 12px;font-weight:800;color:#F59C1C;font-size:12px;letter-spacing:.12em;text-transform:uppercase;margin-bottom:8px}
.contact-us-content .title{font:900 26px/1.15 "Montserrat",sans-serif;color:#0b4552;margin:0 0 8px}
.contact-us-content .text{color:#48646a;margin-bottom:10px}
.contact-us-list ul{list-style:none;margin:0;padding:0;display:grid;gap:8px}
.contact-us-list a{display:flex;gap:10px;align-items:center;padding:8px;border-radius:12px}
.contact-us-list .chip{display:inline-grid;place-items:center;width:36px;height:36px;border-radius:50%;background:#0a8a94;color:#fff}
.form-custom ul{list-style:none;margin:0;padding:0;display:grid;gap:12px}
.form-custom li{position:relative}
.form-custom li .icon{position:absolute;left:12px;top:50%;transform:translateY(-50%);opacity:.7}
.form-custom input, .form-custom textarea{width:100%;background:#fff;border:2px solid #dfecee;border-radius:999px;padding:12px 16px 12px 38px}
.form-custom textarea{border-radius:18px;padding-left:16px}
.form-button, .btn.btn-primary{display:inline-block;background:#0aa0a9;color:#fff;border:0;border-radius:12px;padding:10px 16px;font-weight:800}
.form-button:hover{filter:brightness(1.06)}
.contact-band a{ text-decoration:none }

/* ====== Donation (Bağış) ====== */
.section-padding.bg-light{background:linear-gradient(180deg,#f5fbfb 0,#f6fbf6 100%)}
.section-title .title{font:900 28px/1.15 "Montserrat",sans-serif;color:#0b4552;text-transform:uppercase;letter-spacing:.02em}
.section-title .text{color:#47636a}
.donation-card{background:#fff;border:1px solid #e7eff2;border-radius:20px;box-shadow:0 18px 40px rgba(0,0,0,.08);overflow:hidden}
.donation-card-header{background:linear-gradient(180deg,#e6faf8,#ffffff);padding:16px}
.donation-card-header i{color:#0a8a94}
.donation-card-body{padding:14px 16px}
.donation-card-body .card-title{font-weight:800;color:#0a434a}
.donation-card-body .card-text{color:#47636a}
.progress{height:12px;background:#edf5f6;border-radius:999px;overflow:hidden}
.progress-bar{background:#d9ea56;color:#123;font-weight:800;font-size:12px;line-height:12px}
/* ====== DONATIONS (JA stili) ====== */
.donations{position:relative;padding:70px 0;background:linear-gradient(180deg,#f5fbfb 0,#f6fbf6 100%)}
.donations .topline{position:absolute;left:0;right:0;top:0;height:10px;background:#0aa0a9}
.donations .wrap{max-width:1180px;margin:0 auto;padding:0 24px}
.donations a{text-decoration:none}

/* Başlık */
.donations .head{text-align:center;margin-bottom:22px}
.donations .head h3{font:900 32px/1.12 Montserrat,Inter,sans-serif;color:#0b4552;margin:6px 0 8px;letter-spacing:.01em;text-transform:uppercase}
.donations .head p{color:#47636a;margin:0}
.donations .pill{display:inline-block;background:#e2f4f0;border-radius:999px;padding:6px 12px;font-weight:800;color:#F59C1C;font-size:12px;letter-spacing:.12em;text-transform:uppercase}

/* Kart grid */
.d-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px}

/* Kart */
.d-card{background:#fff;border:1px solid #e7eff2;border-radius:20px;box-shadow:0 18px 40px rgba(0,0,0,.08);overflow:hidden;display:flex;flex-direction:column}

/* Kapak (16:9 oran) */
.d-media{position:relative}
.d-media::before{content:"";display:block;padding-top:56.25%}
.d-media img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}

/* Kapak yoksa ikonlu placeholder */
.d-media-icon{position:absolute;inset:0;display:grid;place-items:center;background:linear-gradient(180deg,#e6faf8,#ffffff)}
.d-media-icon i{font-size:64px;color:#0a8a94;filter:drop-shadow(0 8px 16px rgba(0,0,0,.12))}

/* İçerik */
.d-body{padding:16px 16px 18px;display:flex;flex-direction:column;gap:10px}
.d-title{font-weight:800;color:#0a434a;margin:0}
.d-desc{color:#47636a;margin:0}

/* İlerleme */
.donations .progress{height:12px;background:#edf5f6;border-radius:999px;overflow:hidden}
.donations .progress .bar{height:100%;background:#d9ea56}

/* Metalar ve buton */
.d-meta{display:flex;justify-content:space-between;color:#47636a;font-size:12px}
.btn.donate{align-self:flex-start;background:#0aa0a9;color:#fff;border-radius:10px;padding:10px 14px;font-weight:800}
.btn.donate:hover{filter:brightness(1.06);color:white !important;}

/* Responsive */
@media (max-width:1024px){ .d-grid{grid-template-columns:repeat(2,1fr)} }
@media (max-width:640px){  .d-grid{grid-template-columns:1fr} }

/* ====== Responsive ====== */
@media (max-width: 992px){
  .gallery-description.panel{margin-bottom:14px}
}
.VIpgJd-ZVi9od-aZ2wEe-wOHMyf-ti6hGc {
    -webkit-transition-delay: 0s;
    transition-delay: 0s;
    left: -36px;
    top: -24px;
    display: none;
}

/* ========== Başkan Hakkında ========== */
.baskan-section{position:relative;background-size:cover;background-position:center;padding:70px 0}
.baskan-overlay{position:absolute;inset:0;background:linear-gradient(180deg,rgba(255,255,255,.9),rgba(255,255,255,.75));backdrop-filter:saturate(1.1)}
.baskan-photo img{display:block;width:100%;height:auto;object-fit:cover;aspect-ratio:4/5;min-height:320px;border-radius:20px;box-shadow:0 20px 40px rgba(0,0,0,.18)}
@supports not (aspect-ratio: 1){ .baskan-photo img{height:420px} }
.baskan-card{position:relative;background:#fff;border:1px solid #e8eff1;border-radius:24px;padding:20px 22px;box-shadow:0 24px 50px rgba(0,0,0,.08)}
.baskan-card .pill{display:inline-block;background:#e2f4f0;border-radius:999px;padding:6px 12px;font-weight:800;color:#F59C1C;font-size:12px;letter-spacing:.12em;text-transform:uppercase;margin-bottom:8px}
.baskan-card .g-title{font:900 28px/1.15 "Montserrat",sans-serif;color:#0b4552;margin:2px 0 8px}
.baskan-slogan{color:#48646a;margin:0}
.baskan-socials{display:flex;gap:10px;margin-top:14px}
.baskan-socials a{display:inline-grid;place-items:center;width:38px;height:38px;border-radius:50%;background:#0a8a94;color:#fff}
.baskan-socials a:hover{filter:brightness(1.06)}
.baskan-section a,.baskan-section a:hover{ text-decoration:none }

/* ========== Projeler ========== */
.projeler-section{position:relative;background:#fff;padding:50px 0}
.projeler-topline{position:absolute;left:0;right:0;top:0;height:10px;background:#0aa0a9}
.projeler-title{text-transform:uppercase;letter-spacing:.03em;color:#0a434a}
.owl-carousel-proje .item{padding:8px}
.proje-card{display:grid;grid-template-columns:1.05fr 0.95fr;border:1px solid #e7eff2;border-radius:20px;overflow:hidden;box-shadow:0 24px 50px rgba(0,0,0,.08);background:#fff}
.proje-media{position:relative}
.proje-media a,.proje-media a:hover{ text-decoration:none }
.proje-media img{display:block;width:100%;height:18pc;object-fit:cover;aspect-ratio:16/9;min-height:260px}
@supports not (aspect-ratio: 1){ .proje-media img{height:320px} }
.proje-content{padding:18px 20px;display:flex;flex-direction:column;gap:10px}
.proje-content .g-title a{color:#0a434a;text-decoration:none}
.proje-spot{color:#47636a;margin:0}
.proje-cta {
    align-self: flex-start;
    display: inline-block;
    background: #17a2b8;
    color: #ffffff;
    font-weight: 800;
    padding: 10px 14px;
    border-radius: 10px;
    text-decoration: none;
}
.proje-cta:hover{filter:brightness(1.05);color:white;}
.btn-projeler{display:inline-block;margin-top:10px;padding:10px 18px;border-radius:999px;background:#0aa0a9;color:#fff;font-weight:800;text-decoration:none}
.btn-projeler:hover{filter:brightness(1.06)}
.tumunu-gor{text-align:center;margin-top:10px}
.projeler-section a,.projeler-section a:hover{ text-decoration:none }

/* responsive */
@media (max-width:992px){ .proje-card{grid-template-columns:1fr} }

/* ===== CONNECT WITH US — Instagram (JA stili) ===== */
.insta-band{background:#fff;position:relative;padding:48px 0 38px;overflow-x:hidden}
.insta-band .topline{position:absolute;left:0;right:0;top:0;height:10px;background:#0aa0a9}
.insta-band h4{font:900 28px/1.1 "Montserrat",sans-serif;margin:10px 0 16px;color:#0a434a}
.insta-band .wrap{max-width:1180px;margin:0 auto;padding:0 24px;overflow-x:hidden}
.ig-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px}
.ig-controls{display:flex;gap:10px}

/* kart */
.ig-card{border:1px solid #eaf0f2;border-radius:18px;overflow:hidden;background:#fff;box-shadow:0 10px 30px rgba(0,0,0,.08)}
.ig-card a{text-decoration:none !important;color:#123;display:block}

/* medya alanı — 4:5 görünüm */
.ig-media{position:relative;background:#e9f4f6}
.ig-media::before{content:"";display:block;padding-top:125%}
.ig-media img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}
.ig-media::after{
  content:"";position:absolute;inset:0;background:
    radial-gradient(160px 110px at 16% 16%, rgba(255,255,255,.9), transparent 60%),
    radial-gradient(200px 140px at 84% 28%, rgba(255,255,255,.5), transparent 70%);
  opacity:.18;pointer-events:none
}
.ig-chip{position:absolute;left:10px;bottom:10px;background:rgba(255,255,255,.86);
  padding:6px 10px;border-radius:999px;font-weight:800;font-size:12px;color:#0a434a}
.ig-play{position:absolute;inset:0;display:grid;place-items:center;font-size:44px;color:#fff;text-shadow:0 6px 16px rgba(0,0,0,.45)}

/* gövde */
.ig-body{padding:12px 14px;color:#4b6368}
.ig-body p{margin:0 0 10px}
.ig-meta{display:flex;gap:14px;font-weight:700;color:#0a434a}

/* hover */
.ig-card:hover{transform:translateY(-2px);box-shadow:0 16px 34px rgba(0,0,0,.10);transition:.15s}

/* Owl nav (varsa) */
.ig-nav{display:flex;gap:10px;justify-content:flex-end;margin:6px 0 12px}
.ig-nav .owl-prev,.ig-nav .owl-next{
  width:40px;height:40px;border-radius:50%;display:inline-grid;place-items:center;
  background:#0aa0a9;color:#fff;font-weight:900;font-size:18px;
  box-shadow:0 10px 20px rgba(10,160,169,.25);transition:.15s
}
.ig-nav .owl-prev:hover,.ig-nav .owl-next:hover{filter:brightness(1.08)}
.ig-nav .owl-prev[disabled],.ig-nav .owl-next[disabled]{opacity:.4}

/* Fallback için yatay kaydırmalı şerit (Owl yoksa JS bu sınıfı ekler) */
.ig-strip{display:grid;grid-auto-flow:column;grid-auto-columns:calc(33.33% - 12px);gap:16px;overflow-x:auto;scroll-snap-type:x mandatory;padding-bottom:8px}
.ig-strip::-webkit-scrollbar{height:8px}
.ig-strip::-webkit-scrollbar-thumb{background:#d7e9ec;border-radius:999px}

/* responsive */
@media (max-width:1024px){ 
  .ig-nav{justify-content:center} 
  .ig-strip{grid-auto-columns:calc(50% - 10px)} 
  .ig-head{flex-direction:column;gap:10px}
  .ig-head h4{text-align:center}
  .ig-controls{justify-content:center;gap:8px}
}
@media (max-width:640px){ 
  .ig-strip{grid-auto-columns:90%} 
  .insta-band .wrap{padding:0 15px}
  .ig-card{margin:0 5px}
}
/* ========== Modern Contact Section ========== */
.modern-contact-section {
  position: relative;
  padding: 100px 0;
  background: #ffffff;
  overflow: hidden;
}

.modern-contact-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 10px;
  background: #d9ea56;
}

/* Header */
.contact-header {
  margin-bottom: 3rem;
}

.contact-pill {
  display: inline-block;
  background: #d9ea56;
  color: #2c5f5f;
  padding: 10px 20px;
  border-radius: 30px;
  font-size: 13px;
  font-weight: 700;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  margin-bottom: 1.5rem;
}

.contact-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: #2c5f5f;
  line-height: 1.2;
  margin: 0;
  max-width: 750px;
  margin: 0 auto;
}

/* Modern Card */
.modern-contact-card {
  background: white;
  border-radius: 20px;
  padding: 2.5rem;
  box-shadow: 0 20px 60px rgba(0,0,0,0.08);
  border: 1px solid #e9ecef;
}

/* Form */
.modern-contact-form {
  margin: 0;
}

.form-group-modern {
  margin-bottom: 1.5rem;
}

.form-control-modern {
  width: 100%;
  padding: 16px 20px;
  border: 2px solid #e9ecef;
  border-radius: 50px;
  font-size: 16px;
  font-weight: 400;
  color: #2c5f5f;
  background: white;
  transition: all 0.3s ease;
  box-sizing: border-box;
}

.form-control-modern:focus {
  outline: none;
  border-color: #2c5f5f;
  box-shadow: 0 0 0 4px rgba(44,95,95,0.1);
}

.form-control-modern::placeholder {
  color: #95a5a6;
}

select.form-control-modern {
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%232c5f5f' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 20px center;
  padding-right: 45px;
  cursor: pointer;
}

/* Button */
.btn-sign-up {
  background: #2c5f5f;
  color: white;
  padding: 16px 60px;
  border: none;
  border-radius: 50px;
  font-size: 16px;
  font-weight: 700;
  letter-spacing: 1px;
  text-transform: uppercase;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 10px 30px rgba(44,95,95,0.3);
}

.btn-sign-up:hover {
  background: #1e4a4a;
  transform: translateY(-2px);
  box-shadow: 0 15px 40px rgba(44,95,95,0.4);
}

.btn-sign-up:active {
  transform: translateY(0);
}

/* Responsive */
@media (max-width: 992px) {
  .modern-contact-section {
    padding: 80px 0;
  }
  
  .contact-title {
    font-size: 2rem;
  }
  
  .modern-contact-card {
    padding: 2rem;
  }
}

@media (max-width: 768px) {
  .modern-contact-section {
    padding: 60px 0;
  }
  
  .contact-title {
    font-size: 1.75rem;
  }
  
  .modern-contact-card {
    padding: 1.5rem;
  }
  
  .form-control-modern {
    padding: 14px 18px;
    font-size: 15px;
  }
  
  .btn-sign-up {
    width: 100%;
    padding: 16px 40px;
  }
}

@media (max-width: 576px) {
  .contact-pill {
    font-size: 11px;
    padding: 8px 16px;
  }
  
  .contact-title {
    font-size: 1.5rem;
  }
  
  .modern-contact-card {
    padding: 1.25rem;
  }
}
.footer-ust::before {
    background: linear-gradient(rgb(229, 57, 53), rgb(183, 28, 28)) !important;
    
}
</style>