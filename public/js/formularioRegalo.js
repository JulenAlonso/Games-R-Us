document.addEventListener("DOMContentLoaded", () => {
    const giftForm = document.getElementById("gift-form");
    const paymentForm = document.getElementById("payment-form");
    const submitButton = document.getElementById("submit");

    // Validar campos del formulario de regalo
    giftForm.addEventListener("submit", (event) => {
        event.preventDefault();

        const giftName = document.getElementById("gift_name");
        const giftEmail = document.getElementById("gift_email");
        const giftMessage = document.getElementById("gift_message");
        const productList = document.getElementById("product_list");

        if (!giftName.value.trim()) {
            alert("Por favor, ingresa el nombre del destinatario.");
            return;
        }

        if (!validateEmail(giftEmail.value.trim())) {
            alert("Por favor, ingresa un correo electrónico válido para el destinatario.");
            return;
        }

        if (!giftMessage.value.trim()) {
            alert("Por favor, escribe un mensaje para el destinatario.");
            return;
        }

        if (!productList.value.trim()) {
            alert("Por favor, ingresa al menos un producto para regalar.");
            return;
        }

        // Validar los campos de pago también
        const requiredFields = ["name", "ap1", "ap2", "email", "tlf", "fechaNac", "calle", "num", "piso", "letra", "cp", "localidad", "pais"];

        for (const field of requiredFields) {
            const input = document.getElementById(field);
            if (!input.value.trim()) {
                alert(`Por favor, completa el campo de información de pago: ${field}`);
                return;
            }
        }

        if (!validateEmail(document.getElementById("email").value.trim())) {
            alert("Por favor, ingresa un correo electrónico válido para la información de pago.");
            return;
        }

        alert("Formulario de regalo enviado correctamente.");
    });

    // Validar campos del formulario de pago
    paymentForm.addEventListener("submit", (event) => {
        event.preventDefault();

        const requiredFields = ["name", "ap1", "ap2", "email", "tlf", "fechaNac", "calle", "num", "piso", "letra", "cp", "localidad", "pais"];

        for (const field of requiredFields) {
            const input = document.getElementById(field);
            if (!input.value.trim()) {
                alert(`Por favor, completa el campo: ${field}`);
                return;
            }
        }

        if (!validateEmail(document.getElementById("email").value.trim())) {
            alert("Por favor, ingresa un correo electrónico válido.");
            return;
        }

        alert("Formulario de pago enviado correctamente.");
    });

    // Formatear número de tarjeta
    const cardNumberInput = document.getElementById("input-number");
    const cleaveCard = new Cleave(cardNumberInput, {
        creditCard: true,
    });

    // Sincronizar datos de la tarjeta con la vista previa
    const cardNameInput = document.getElementById("input-name");
    const cardMonthInput = document.getElementById("input-month");
    const cardYearInput = document.getElementById("input-year");
    const cardCvcInput = document.getElementById("input-cvc");

    cardNumberInput.addEventListener("input", () => {
        document.getElementById("card-number").textContent = cardNumberInput.value || "0000 0000 0000 0000";
    });

    cardNameInput.addEventListener("input", () => {
        document.getElementById("card-name").textContent = cardNameInput.value || "Pepe Navarro";
    });

    cardMonthInput.addEventListener("input", () => {
        document.getElementById("card-month").textContent = cardMonthInput.value || "00";
    });

    cardYearInput.addEventListener("input", () => {
        document.getElementById("card-year").textContent = cardYearInput.value || "00";
    });

    cardCvcInput.addEventListener("input", () => {
        document.getElementById("card-cvc").textContent = cardCvcInput.value || "000";
    });

    // Validar email
    function validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Manejo del botón de envío general
    submitButton.addEventListener("click", () => {
        const giftValid = giftForm.checkValidity();
        const paymentValid = paymentForm.checkValidity();

        if (giftValid && paymentValid) {
            alert("Todos los formularios se han completado correctamente.");
        } else {
            alert("Por favor, completa todos los campos requeridos.");
        }
    });
});
