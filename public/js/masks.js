document.addEventListener("DOMContentLoaded", function () {
    // Телефон
    document.querySelectorAll('input[data-mask="phone"]').forEach(input => {
        IMask(input, {
            mask: '+{7} (000) 000-00-00'
        });
    });

    // ИНН (10 или 12)
    document.querySelectorAll('input[data-mask="inn"]').forEach(input => {
        IMask(input, {
            mask: [
                { mask: '0000000000' },
                { mask: '000000000000' }
            ]
        });
    });

    // ОГРНИП (15)
    document.querySelectorAll('input[data-mask="ogrnip"]').forEach(input => {
        IMask(input, {
            mask: '000000000000000'
        });
    });

    // ОГРН (13)
    document.querySelectorAll('input[data-mask="ogrn"]').forEach(input => {
        IMask(input, {
            mask: '0000000000000'
        });
    });
});
