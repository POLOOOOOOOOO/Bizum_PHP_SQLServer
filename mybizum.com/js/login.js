document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("login-form");

  form?.addEventListener("submit", function (e) {
    e.preventDefault();

    const username = document.getElementById("username").value.trim();
    //const ssid = document.getElementById("ssid").value.trim();
    const password = document.getElementById("password").value.trim();

    const url = `http://ws.mybizum.com:8080/com/ws.php?action=login&username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`;

    fetch(url)
      .then(res => res.text())
      .then(str => new DOMParser().parseFromString(str, "application/xml"))
      .then(xml => {
        console.log("XML:", new XMLSerializer().serializeToString(xml));

        const numError = xml.getElementsByTagName("num_error")[0]?.textContent;

        if (numError === "0") {
          const ssid = xml.getElementsByTagName("SSID")[0]?.textContent;

          sessionStorage.setItem("username", username);
          if (ssid) {
            sessionStorage.setItem("ssid", ssid);
          }

          window.location.href = "/pages/dashboard.html";
        } else {
          const msg = xml.getElementsByTagName("message_error")[0]?.textContent || "Error desconocido";
          const code = xml.getElementsByTagName("num_error")[0]?.textContent || "Desconocido";

          window.location.href = `/pages/error.html?msg=${encodeURIComponent(msg)}&code=${encodeURIComponent(code)}`;
        }
      })
      .catch(err => {
        console.error("Error en el login:", err);
        alert("Error de red");
      });
  });
});
