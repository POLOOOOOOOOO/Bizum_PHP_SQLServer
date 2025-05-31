document.addEventListener("DOMContentLoaded", function () {
    const name = sessionStorage.getItem("name") || "Usuario";
    const welcomeEl = document.getElementById("welcome-message");
    welcomeEl.textContent = `Bienvenido, ${name}`;
});
