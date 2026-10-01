<script>
function switchLang(lang, btn) {
    document.querySelectorAll('.bi-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.bi-panel').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('panel-' + lang).classList.add('active');
}
</script>
