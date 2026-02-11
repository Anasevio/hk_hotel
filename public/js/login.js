document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault(); // mencegah submit default

    const username = this.username.value;
    const password = this.password.value;

    try {
        const response = await fetch('/api/login', { // endpoint API login
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}' // penting untuk Laravel session
            },
            body: JSON.stringify({ username, password })
        });

        const data = await response.json();

        if (data.status === 'success') {
            // redirect sesuai role
            window.location.href = data.redirect;
        } else {
            alert(data.message); // tampilkan error login
        }
    } catch (err) {
        console.error(err);
        alert('Terjadi kesalahan pada server.');
    }
});
