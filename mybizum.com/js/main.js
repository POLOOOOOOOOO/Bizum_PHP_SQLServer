document.addEventListener("DOMContentLoaded", function () {
    const passwordContainer = document.getElementById("password");

    if (passwordContainer) {
        passwordContainer.addEventListener("input", function () {
            const password = this.value;

            if (password === '') {
                document.getElementById("result").innerText = '';
                return
            }


            fetch(`http://ws.mybizum.com:8080/com/ws.php?action=checkpassword&password=${encodeURIComponent(password)}`)


                .then(response => response.text())
                .then(data => {
                    const parser = new DOMParser();
                    const xml = parser.parseFromString(data, "text/xml");
                    const messageError = xml.getElementsByTagName("PasswordStrength")[0];

                    if (messageError) {
                        document.getElementById("result").innerText = messageError.textContent;
                    } else {
                        console.log("Error en la contraseña");
                    }
                })
                .catch(error => console.error("Error en la solicitud:", error));
        });
    }
});

// document.querySelector(".auth-form").addEventListener("submit", function (e) {
//     e.preventDefault();

//     const form = e.target;
//     const data = new URLSearchParams(new FormData(form));

//     fetch(form.action + "?" + data.toString())
//         .then(res => res.text())
//         .then(text => {
//             const xml = new DOMParser().parseFromString(text, "text/xml");
//             const success = xml.querySelector("Success");
//             const error = xml.querySelector("Error");

//             if (success) {
//                 window.location.href = "profile.php";
//             } else if (error) {
//                 alert(error.textContent);
//             } else {
//                 alert("Respuesta inesperada.");
//             }
//         })
//         .catch(err => {
//             alert("Error al hacer login.");
//             console.error(err);
//         });
// });