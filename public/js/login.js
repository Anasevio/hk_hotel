document
    .getElementById("loginForm")
    .addEventListener("submit", async function (e) {
        e.preventDefault();

        const username = this.username.value;
        const password = this.password.value;

        const token = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");

        try {
            const response = await fetch("/login", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": token,
                },
                body: JSON.stringify({ username, password }),
            });

            if (!response.ok) {
                throw new Error("HTTP error " + response.status);
            }

            const data = await response.json();

            if (data.status === "success") {
                window.location.href = data.redirect;
            } else {
                alert(data.message);
            }
        } catch (err) {
            console.error(err);
            alert("Terjadi kesalahan pada server.");
        }
    });
