// Kutip — Forum JS

function toggleReplyForm(id) {
    const el = document.getElementById(id);
    if (!el) return;
    const isHidden = el.classList.contains('hidden');
    el.classList.toggle('hidden', !isHidden);
    if (isHidden) {
        const textarea = el.querySelector('textarea');
        if (textarea) textarea.focus();
    }
}

// Auto-resize textareas
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('textarea').forEach(function (ta) {
        ta.addEventListener('input', function () {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    });

    // Character counter for post textarea
    document.querySelectorAll('textarea[maxlength]').forEach(function (ta) {
        const max = parseInt(ta.getAttribute('maxlength'));
        const counter = document.createElement('div');
        counter.className = 'char-counter';
        counter.style.cssText = 'text-align:right;font-size:0.7rem;color:#4b5563;margin-top:0.25rem;';
        ta.parentNode.insertBefore(counter, ta.nextSibling);

        function update() {
            const left = max - ta.value.length;
            counter.textContent = left + ' karakter tersisa';
            counter.style.color = left < 100 ? '#f59e0b' : '#4b5563';
        }
        ta.addEventListener('input', update);
        update();
    });
});

// CSRF token for fetch requests
window.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
