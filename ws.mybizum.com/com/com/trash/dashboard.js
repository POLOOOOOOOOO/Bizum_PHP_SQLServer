document.getElementById("bizumForm").addEventListener("submit", async function (e) {
    e.preventDefault();

    const from = e.target.from.value;
    const to = e.target.to.value;
    const amount = e.target.amount.value;

    const url = `http://ws.mybizum.com:8080/com/ws.php?action=enviar&from=${encodeURIComponent(from)}&to=${encodeURIComponent(to)}&amount=${encodeURIComponent(amount)}`;

    try {
        const res = await fetch(url);
        const data = await res.json();

        const result = document.getElementById("bizumResult");
        if (data.status === "success") {
            result.innerHTML = `<p style="color: green;">✅ ${data.message}<br>De: ${data.transaction.from} → A: ${data.transaction.to}<br>Cantidad: ${data.transaction.amount} €</p>`;
            e.target.reset();
        } else {
            result.innerHTML = `<p style="color: red;">❌ ${data.message}</p>`;
        }
    } catch (error) {
        document.getElementById("bizumResult").innerHTML =
            `<p style="color: red;">⚠️ Error al conectar con el servidor.</p>`;
    }
});
