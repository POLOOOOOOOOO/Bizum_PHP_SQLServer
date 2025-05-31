document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("register-form");

    form?.addEventListener("submit", function (e) {
        e.preventDefault();

        const username = document.getElementById("username").value.trim();
        const name = document.getElementById("name").value.trim();
        const lastname = document.getElementById("lastname").value.trim();
        const password = document.getElementById("password").value.trim();
        const email = document.getElementById("email").value.trim();

        const url = `http://ws.mybizum.com:8080/com/ws.php?action=register&username=${encodeURIComponent(username)}&name=${encodeURIComponent(name)}&lastname=${encodeURIComponent(lastname)}&password=${encodeURIComponent(password)}&email=${encodeURIComponent(email)}`;

        fetch(url)
            .then(res => res.text())
            .then(str => new DOMParser().parseFromString(str, "application/xml"))
            .then(xml => {
                console.log("XML:", new XMLSerializer().serializeToString(xml));

                const numError = xml.getElementsByTagName("num_error")[0]?.textContent;

                if (numError === "0") {
                    sessionStorage.setItem("username", username);
                    window.location.href = "/pages/dashboard.html";
                } else {
                    const msg = xml.getElementsByTagName("message_error")[0]?.textContent || "Error desconocido";
                    const code = xml.getElementsByTagName("num_error")[0]?.textContent || "Desconocido";

                    window.location.href = `/pages/error.html?msg=${encodeURIComponent(msg)}&code=${encodeURIComponent(code)}`;


                }
            })
            .catch(err => {
                console.error("Error en el registro:", err);
                alert("Error de red");
            });
    });
});

