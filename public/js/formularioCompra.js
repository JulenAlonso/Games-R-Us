// Variables de los inputs de la tarjeta
const inputName = document.querySelector("#input-name");
const inputNumber = document.querySelector("#input-number");
const inputMonth = document.querySelector("#input-month");
const inputYear = document.querySelector("#input-year");
const inputCVC = document.querySelector("#input-cvc");
const cardNumber = document.querySelector("#card-number");
const cardName = document.querySelector("#card-name");
const cardMonth = document.querySelector("#card-month");
const cardYear = document.querySelector("#card-year");
const cardCVC = document.querySelector("#card-cvc");
const formUser = document.querySelector("#infoUserPagos");
const formCard = document.querySelector("#infoUserTarjeta");

// Animaciones de la tarjeta de pago
inputName.addEventListener("input", () => {
    cardName.innerText = inputName.value || "Pepe Navarro";
});

new Cleave("#input-number", { creditCard: true });

inputNumber.addEventListener("input", () => {
    cardNumber.innerText = inputNumber.value || "0000 0000 0000 0000";
});

inputMonth.addEventListener("input", () => {
    cardMonth.innerText = inputMonth.value || "00";
});

inputYear.addEventListener("input", () => {
    cardYear.innerText = inputYear.value || "00";
});

inputCVC.addEventListener("input", () => {
    cardCVC.innerText = inputCVC.value || "000";
});

function existeCookie(nombre) {
    return document.cookie.split('; ').some(cookie => cookie.startsWith(nombre + '='));
}

async function comprobarTarjetaSOAP(numeroTarjeta) {
    const url = "http://localhost/Games-R-Us/src/controladores/soap.php";

    const soapRequest = `
        <SOAP-ENV:Envelope 
            xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" 
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:xsd="http://www.w3.org/2001/XMLSchema">
            <SOAP-ENV:Body>
                <comprobarTarjeta xmlns="http://localhost/Games-R-Us/soap">
                    <numeroTarjeta xsi:type="xsd:string">${numeroTarjeta}</numeroTarjeta>
                </comprobarTarjeta>
            </SOAP-ENV:Body>
        </SOAP-ENV:Envelope>
    `;

    try {
        const response = await fetch(url, {
            method: "POST",
            mode: "cors",
            headers: {
                "Content-Type": "text/xml; charset=utf-8",
                "SOAPAction": "http://localhost/Games-R-Us/soap/comprobarTarjeta"
            },
            body: soapRequest
        });

        if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);

        const textResponse = await response.text();

        const parser = new DOMParser();
        const xmlDoc = parser.parseFromString(textResponse, "text/xml");

        let resultado = xmlDoc.getElementsByTagName("return")[0]?.textContent.trim();
        resultado = resultado === "1" || resultado.toLowerCase() === "true";

        console.log("Resultado:", resultado ? "✅ Tarjeta válida" : "❌ Tarjeta inválida");

        return resultado;
    } catch (error) {
        console.error("❌ Error en la solicitud SOAP:", error);
    }
}

if (existeCookie("gift_user") && existeCookie("gift_game")) {
    document.querySelector(".submit-btn").addEventListener("click", async (event) => {
        event.preventDefault();
    
        // Obtener datos del usuario
        const formUser = document.querySelector("#infoUserPagos");
        const formCard = document.querySelector("#infoUserTarjeta");
    
        if (!formUser || !formCard) {
            console.error("Error: No se encontraron los formularios.");
            return;
        }
    
        const formDataUser = new FormData(formUser);
        const userInfo = {};
        formDataUser.forEach((value, key) => {
            userInfo[key] = value;
        });
    
        // Obtener datos de la tarjeta
        const formDataCard = new FormData(formCard);
        const cardInfo = {};
        formDataCard.forEach((value, key) => {
            // Si el campo es el número de tarjeta, eliminar espacios
            cardInfo[key] = key === "input-number" ? value.replace(/\s+/g, '') : value;
        });
    
        // Agregar el parámetro de acción
        userInfo["accion"] = "procesarRegaloUser";
        userInfo["gift_user"] = document.cookie.split('; ').find(cookie => cookie.startsWith("gift_user=")).split('=')[1];
        userInfo["gift_game"] = document.cookie.split('; ').find(cookie => cookie.startsWith("gift_game=")).split('=')[1];
    
        // Crear objeto URLSearchParams
        const params = new URLSearchParams();
    
        // Agregar datos del usuario
        Object.entries(userInfo).forEach(([key, value]) => {
            params.append(key, value);
        });
    
        // Agregar datos de la tarjeta
        Object.entries(cardInfo).forEach(([key, value]) => {
            params.append(key, value);
        });
    
        console.log("Enviando datos al backend:", params.toString());
    
        try {
            const response = await fetch("/Games-R-Us/public/index.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: params.toString()
            });
    
            const result = await response.json();
            if (result.success) {
                alert("Regalo realizado con éxito");
                document.cookie = "gift_user=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                document.cookie = "gift_game=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
            } else {
                alert("Error en el pago: " + result.message);
            }
        } catch (error) {
            console.error("Error en la solicitud:", error);
            alert("Hubo un problema al procesar el pago.");
        }
    });
} else {
    document.querySelector(".submit-btn").addEventListener("click", async (event) => {
        event.preventDefault();
    
        // Obtener datos del usuario
        const formUser = document.querySelector("#infoUserPagos");
        const formCard = document.querySelector("#infoUserTarjeta");
    
        if (!formUser || !formCard) {
            console.error("Error: No se encontraron los formularios.");
            return;
        }
    
        const formDataUser = new FormData(formUser);
        const userInfo = {};
        formDataUser.forEach((value, key) => {
            userInfo[key] = value;
        });
    
        // Obtener datos de la tarjeta
        const formDataCard = new FormData(formCard);
        const cardInfo = {};

        // Comprobamos la terjeta
        const tarjeta = formDataCard.get("input-number").replace(/\s+/g, '');

        try {
            // Esperar la validación de la tarjeta
            const esValida = await comprobarTarjetaSOAP(tarjeta);
    
            if (!esValida) {
                alert("La tarjeta tiene que ser válida");
                return;
            }
        
        } catch (error) {
            console.error("Error en la solicitud:", error);
            alert("Hubo un problema al procesar el pago.");
        }

        formDataCard.forEach((value, key) => {
            // Si el campo es el número de tarjeta, eliminar espacios
            cardInfo[key] = key === "input-number" ? value.replace(/\s+/g, '') : value;
        });
    
        // Agregar el parámetro de acción
        userInfo["accion"] = "procesarPagoUser";
    
        // Crear objeto URLSearchParams
        const params = new URLSearchParams();
    
        // Agregar datos del usuario
        Object.entries(userInfo).forEach(([key, value]) => {
            params.append(key, value);
        });
    
        // Agregar datos de la tarjeta
        Object.entries(cardInfo).forEach(([key, value]) => {
            params.append(key, value);
        });
    
        console.log("Enviando datos al backend:", params.toString());

        try {
            const response = await fetch("/Games-R-Us/public/index.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: params.toString()
            });
    
            const result = await response.json();
            if (result.success) {
                alert("Pago realizado con éxito");
                document.cookie = "purchase_total=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                document.cookie = "purchase_user=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                window.location = "/Games-R-Us/public/";
            } else {
                alert("Error en el pago: " + result.message);
            }
        } catch (error) {
            console.error("Error en la solicitud:", error);
            alert("Hubo un problema al procesar el pago.");
        }
        
    });
}



