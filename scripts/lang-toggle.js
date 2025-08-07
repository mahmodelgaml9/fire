function setLangCookie(lang) {
    document.cookie = 'site_lang=' + lang + ';path=/;max-age=' + (60*60*24*365) + ';SameSite=Lax';
}
function getLangCookie() {
    const m = document.cookie.match(/(?:^|; )site_lang=([^;]+)/);
    return m ? m[1] : null;
}
function bindLangToggle() {
    const langBtn = document.getElementById('lang-toggle');
    if (!langBtn) return;
    langBtn.onclick = function() {
        const nextLang = langBtn.textContent.trim() === 'English' ? 'en' : 'ar';
        setLangCookie(nextLang);
        location.reload();
    };
}
bindLangToggle(); 