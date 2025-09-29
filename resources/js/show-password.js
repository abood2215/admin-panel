document.querySelectorAll('.toggle-password').forEach(function(btn) {
    const input = document.getElementById(btn.dataset.toggle);

    // SVGs للعين المغلقة والمفتوحة
    const closedEye = `
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none"
            viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.411-4.118m3.509-2.343A9.969 9.969 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.973 9.973 0 01-4.676 5.569M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 3l18 18" />
        </svg>
    `;
    const openEye = `
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none"
            viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        </svg>
    `;

    btn.innerHTML = closedEye;

    btn.addEventListener('mousedown', function() {
        input.type = 'text';
        btn.innerHTML = openEye;
    });
    btn.addEventListener('mouseup', function() {
        input.type = 'password';
        btn.innerHTML = closedEye;
    });
    btn.addEventListener('mouseleave', function() {
        input.type = 'password';
        btn.innerHTML = closedEye;
    });
});
