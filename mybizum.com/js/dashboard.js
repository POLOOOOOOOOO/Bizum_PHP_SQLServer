let ajax = null;

// Enviar Bizum
document.getElementById("bizumForm").addEventListener("submit", function (e) {
    e.preventDefault();

    const ssid = sessionStorage.getItem("ssid");
    const to = e.target.to.value;
    const amount = e.target.amount.value;

    if (!ssid) {
        alert("❌ No hay sesión activa.");
        return;
    }

    const url = `http://ws.mybizum.com:8080/com/ws.php?action=enviar&from=${encodeURIComponent(ssid)}&to=${encodeURIComponent(to)}&amount=${encodeURIComponent(amount)}`;
    ajax = new clsAjax(url, "bizumApp");
    ajax.Call();
});

// Mostrar resultado del envío
document.addEventListener("CALL_RETURNED", function () {
    const result = document.getElementById("bizumResult");

    try {
        const parser = new DOMParser();
        const xmlDoc = parser.parseFromString(ajax.xml, "text/xml");

        const status = xmlDoc.querySelector("Status")?.textContent;
        const from = xmlDoc.querySelector("From")?.textContent;
        const to = xmlDoc.querySelector("To")?.textContent;
        const amount = xmlDoc.querySelector("Amount")?.textContent;
        const blockId = xmlDoc.querySelector("BlockID")?.textContent;

        if (status === "success") {
            result.innerHTML = `<p style="color: green;">✅ Bizum enviado correctamente.<br>De: ${from} → A: ${to}<br>Cantidad: ${amount} €<br>Bloque: ${blockId}</p>`;
            document.getElementById("bizumForm").reset();
        } else {
            const message = xmlDoc.querySelector("Message")?.textContent || "Error desconocido";
            result.innerHTML = `<p style="color: red;">❌ ${message}</p>`;
        }
    } catch (error) {
        result.innerHTML = `<p style="color: red;">⚠️ Error al procesar la respuesta XML.</p>`;
    }
});

// Logout con SSID
const logoutButton = document.getElementById("logout-btn");
if (logoutButton) {
    logoutButton.addEventListener("click", function () {
        console.log("Botón logout clicado");

        const ssid = sessionStorage.getItem("ssid");
        if (!ssid) {
            alert("❌ No hay sesión activa.");
            return;
        }

        const url = `http://ws.mybizum.com:8080/com/ws.php?action=logout&ssid=${encodeURIComponent(ssid)}`;
        const ajax = new clsAjax(url, this);

        document.addEventListener("CALL_RETURNED", function () {
            console.log("Logout exitoso. Limpiando sessionStorage y redirigiendo.");
            sessionStorage.clear();
            window.location.href = "/pages/login.html";
        }, { once: true });

        ajax.Call();
    });
}

// Autocompletar remitente y verificar sesión
document.addEventListener("DOMContentLoaded", () => {
    const ssid = sessionStorage.getItem("ssid");
    const username = sessionStorage.getItem("username");
    if (!ssid) {
        window.location.href = "/pages/login.html";
        return;
    }

    const welcome = document.getElementById("userWelcome");
    if (welcome) {
        welcome.textContent = `Bienvenido, ${username}`;
    }

    const remitenteInput = document.getElementById("from");
    if (remitenteInput) {
        remitenteInput.value = ssid;
        remitenteInput.readOnly = true;
    }
});

function actualizarSaldo() {
    const ssid = sessionStorage.getItem("ssid");

    if (!ssid) return;

    fetch(`http://ws.mybizum.com:8080/com/ws.php?action=getbalance&ssid=${encodeURIComponent(ssid)}`)
        .then(res => res.text())
        .then(xml => {
            const parser = new DOMParser();
            const xmlDoc = parser.parseFromString(xml, "text/xml");

            const balance = xmlDoc.querySelector("Balance")?.textContent;
            const saldoEl = document.getElementById("saldoActual");

            if (saldoEl && balance) {
                saldoEl.textContent = `${balance} €`;
            }
        });
}

setInterval(actualizarSaldo, 15000); // aqui llamamos a la funcion para revisar el saldo
document.addEventListener("DOMContentLoaded", actualizarSaldo);

// function cargarAgenda() {
//     const ssid = sessionStorage.getItem("ssid");
//     if (!ssid) return;

//     const url = `http://ws.mybizum.com:8080/com/ws.php?action=agenda&ssid=${encodeURIComponent(ssid)}`;
//     const ajax = new clsAjax(url, "bizumApp");

//     document.addEventListener("CALL_RETURNED", function () {
//         try {
//             const parser = new DOMParser();
//             const xmlDoc = parser.parseFromString(ajax.xml, "text/xml");

//             const users = xmlDoc.getElementsByTagName("User");
//             const agendaContainer = document.getElementById("agendaUsuarios");
//             agendaContainer.innerHTML = "";

//             if (users.length === 0) {
//                 agendaContainer.innerHTML = "No hay usuarios disponibles.";
//                 return;
//             }

//             for (let i = 0; i < users.length; i++) {
//                 const user = users[i].textContent;

//                 const userItem = document.createElement("div");
//                 userItem.textContent = user;
//                 userItem.classList.add("agenda-item");
//                 userItem.addEventListener("click", () => {
//                     document.querySelector('input[name="to"]').value = user;
//                 });

//                 agendaContainer.appendChild(userItem);
//             }
//         } catch (err) {
//             console.error("Error al cargar la agenda:", err);
//         }
//     }, { once: true });

//     ajax.Call();
// }

//no esta acabada la agenda 

// document.addEventListener("DOMContentLoaded", cargarAgenda);

// function cargarAgenda() {
//     const ssid = sessionStorage.getItem("ssid");
//     if (!ssid) return;

//     const url = `http://ws.mybizum.com:8080/com/ws.php?action=agenda&ssid=${encodeURIComponent(ssid)}`;
//     const ajax = new clsAjax(url, "bizumApp");

//     document.addEventListener("CALL_RETURNED", function () {
//         try {
//             const parser = new DOMParser();
//             const xmlDoc = parser.parseFromString(ajax.xml, "text/xml");

//             const users = xmlDoc.getElementsByTagName("User");
//             const agendaContainer = document.getElementById("agendaUsuarios");
//             agendaContainer.innerHTML = "";

//             if (users.length === 0) {
//                 agendaContainer.innerHTML = "No hay usuarios disponibles.";
//                 return;
//             }

//             for (let i = 0; i < users.length; i++) {
//                 const user = users[i].textContent;

//                 const userItem = document.createElement("div");
//                 userItem.textContent = user;
//                 userItem.classList.add("agenda-item");
//                 userItem.addEventListener("click", () => {
//                     document.querySelector('input[name="to"]').value = user;
//                 });

//                 agendaContainer.appendChild(userItem);
//             }
//         } catch (err) {
//             console.error("Error al cargar la agenda:", err);
//         }
//     }, { once: true });

//     ajax.Call();
// }

// document.addEventListener("DOMContentLoaded", cargarAgenda);
