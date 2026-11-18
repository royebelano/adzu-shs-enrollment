<div class="sv-toast" id="svToast" role="status" aria-live="polite"></div>

<script>
(function () {
    const toast = document.getElementById('svToast');
    let toastTimer;

    window.svToast = function (text) {
        toast.textContent = text;
        toast.classList.add('is-show');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => toast.classList.remove('is-show'), 2200);
    };

    /* Copy reference ID */
    const copyBtn = document.getElementById('svCopy');
    const refCode = document.getElementById('svRefCode');

    if (copyBtn && refCode) {
        copyBtn.addEventListener('click', async () => {
            const text = refCode.textContent.trim();

            try {
                await navigator.clipboard.writeText(text);
            } catch (e) {
                const range = document.createRange();
                range.selectNodeContents(refCode);
                const sel = window.getSelection();
                sel.removeAllRanges();
                sel.addRange(range);
                document.execCommand('copy');
                sel.removeAllRanges();
            }

            copyBtn.textContent = 'Copied';
            copyBtn.classList.add('is-done');
            window.svToast('Reference ID copied');

            setTimeout(() => {
                copyBtn.textContent = 'Copy';
                copyBtn.classList.remove('is-done');
            }, 1600);
        });
    }
})();
</script>
