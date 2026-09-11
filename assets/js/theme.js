document.addEventListener('DOMContentLoaded', function () {
    const helpBtn = document.getElementById('helpBtn');

    if (helpBtn) {
        helpBtn.addEventListener('click', function () {
            alert('Precisa de ajuda? Entre em contato com o suporte pelo email suporte@organiza.com');
        });
    }
});
