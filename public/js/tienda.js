let storeData = []; // Variable global para almacenar los datos de los juegos

async function fetchData(nick = null) {
    try {
        const response = await fetch('/Games-r-us/public/index.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({ accion: 'listadoJuegos' }),
        });

        if (!response.ok) {
            throw new Error("Error al obtener los datos");
        }

        const result = await response.json();
        if (result.success) {
            storeData = result.data; // Guardar los datos en storeData para su uso global
            renderStore(storeData, null, nick);
            renderSidebar(storeData);
        } else {
            console.error("Error en el servidor:", result.message);
        }
    } catch (error) {
        console.error("Error al realizar la solicitud:", error);
    }
}

function renderStore(data, filterCategory = null, nick = null) {
    const storeContainer = document.getElementById('store-container');
    storeContainer.innerHTML = ''; // Limpiar contenido previo

    const filteredData = filterCategory
        ? data.filter(product => product.categoria === filterCategory)
        : data;

    if (filteredData.length === 0) {
        storeContainer.innerHTML = `<p style="color: white; text-align: center;">No hay productos disponibles.</p>`;
        return;
    }

    const categoryContainer = document.createElement('div');
    categoryContainer.classList.add('container');


    filteredData.forEach(product => {

        console.log(product);

        const productElement = document.createElement('div');
        productElement.classList.add('product');
        productElement.innerHTML = `
            <img src="${product.ruta_imagen}" alt="${product.titulo}">
            <div class="divider"></div>
            <div class="product-content">
                <h3>${product.titulo}</h3>
                <p>${product.desarrollador}</p>
                <p><strong>Precio:</strong> ${product.precio}€</p>
            </div>
        `;

        // Evento para abrir el modal
        productElement.addEventListener('click', () => {
            openModal(product, nick);
        });

        categoryContainer.appendChild(productElement);
    });

    storeContainer.appendChild(categoryContainer);
}

function openModal(product, nick) {
    const modalContainer = document.createElement('div');
    modalContainer.classList.add('modal-container');
    modalContainer.innerHTML = `
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <h3 class="modal-title">${product.titulo}</h3>
            
            <div class="modal-body">
                <img class="modal-image" src="${product.ruta_imagen}" alt="${product.titulo}">
                
                <div class="modal-info">
                    <p><strong>Desarrollador:</strong> ${product.desarrollador}</p>
                    <p><strong>Distribuidor:</strong> ${product.distribuidor}</p>
                    <p><strong>Año:</strong> ${product.anio}</p>
                    <p><strong>Género:</strong> ${product.genero.join(", ")}</p>
                    <p><strong>Plataformas:</strong> ${product.sistema.join(", ")}</p>
                    <p class="modal-price"><strong>Precio:</strong> ${product.precio}€</p>
                </div>
            </div>

            <div class="modal-buttons">
                <form method="POST">
                    <input type="hidden" name="compra_gameid" value="${product.id}">
                    <input type="hidden" name="compra_usuarionick" value="${nick}">
                    <input type="submit" class="btn1" name="tienda_comprar" value="Comprar">
                    <button type="button" class="btn2" onclick="openGiftPopout('${product.id}')">Regalar</button>
                </form>
            </div>
        </div>
    `;

    document.body.appendChild(modalContainer);
    modalContainer.style.display = 'flex';

    modalContainer.querySelector('.modal-close').addEventListener('click', closePopout);
}

function renderSidebar(data) {
    const sidebar = document.getElementById('sidebar');
    sidebar.innerHTML = ''; // Limpiar sidebar antes de renderizar

    // Crear la sección de filtros avanzados
    const filterSection = document.createElement('div');
    filterSection.classList.add('sidebar-section');

    filterSection.innerHTML = `
        <h3>Filtros</h3>
        <label for="genreFilter">Género:</label>
        <select id="genreFilter">
            <option value="">Todos</option>
        </select>

        <label for="developerFilter">Desarrollador:</label>
        <select id="developerFilter">
            <option value="">Todos</option>
        </select>

        <label for="yearFilter">Año:</label>
        <select id="yearFilter">
            <option value="">Todos</option>
        </select>
    `;

    sidebar.appendChild(filterSection);

    // Obtener valores únicos para los filtros
    const genres = [...new Set(data.flatMap(product => product.genero))];
    const developers = [...new Set(data.map(product => product.desarrollador))];
    const years = [...new Set(data.map(product => product.anio))];

    // Llenar los select con las opciones obtenidas
    populateFilterOptions('genreFilter', genres);
    populateFilterOptions('developerFilter', developers);
    populateFilterOptions('yearFilter', years);

    // Event Listeners para los filtros
    document.getElementById('genreFilter').addEventListener('change', applyFilters);
    document.getElementById('developerFilter').addEventListener('change', applyFilters);
    document.getElementById('yearFilter').addEventListener('change', applyFilters);

    // Crear la sección de categorías
    const categorySection = document.createElement('div');
    const categoryList = document.createElement('ul');
    categoryList.id = 'category-list';

    const categories = [...new Set(data.map(product => product.categoria))];

    // Opción para ver todos los juegos
    const allCategoriesItem = document.createElement('li');
    allCategoriesItem.textContent = 'Todos los juegos';
    allCategoriesItem.addEventListener('click', () => renderStore(data));
    categoryList.appendChild(allCategoriesItem);

    categorySection.appendChild(categoryList);
    sidebar.appendChild(categorySection);
}

// Función para llenar los select de filtros
function populateFilterOptions(filterId, options) {
    const filterElement = document.getElementById(filterId);
    options.forEach(option => {
        if (option) { // Evitar valores vacíos
            const opt = document.createElement('option');
            opt.value = option;
            opt.textContent = option;
            filterElement.appendChild(opt);
        }
    });
}

// Función para aplicar los filtros
function applyFilters() {
    const selectedGenre = document.getElementById('genreFilter').value;
    const selectedDeveloper = document.getElementById('developerFilter').value;
    const selectedYear = document.getElementById('yearFilter').value;

    let filteredData = [...storeData]; // Usar storeData como base para filtrar

    if (selectedGenre) {
        filteredData = filteredData.filter(game => game.genero.includes(selectedGenre));
    }
    if (selectedDeveloper) {
        filteredData = filteredData.filter(game => game.desarrollador === selectedDeveloper);
    }
    if (selectedYear) {
        filteredData = filteredData.filter(game => game.anio === selectedYear);
    }

    renderStore(filteredData); // Mostrar solo los juegos filtrados
}

function openGiftPopout(gameId) {
    const giftModal = document.createElement('div');
    giftModal.classList.add('gift-modal-container');
    giftModal.innerHTML = `
        <div class="gift-modal-content">
            <span class="modal-close" onclick="closePopout()">&times;</span>
            <h3>Regalar Juego</h3>
            <p>Introduce el nombre de usuario al que deseas regalar este juego:</p>
            <input type="text" id="giftUser" placeholder="Nombre de usuario" required>
            <div class="gift-modal-buttons">
                <button class="btn1" onclick="sendGift('${gameId}')">Enviar Regalo</button>
                <button class="btn2" onclick="closePopout()">Cancelar</button>
            </div>
        </div>
    `;

    document.body.appendChild(giftModal);
    giftModal.style.display = 'flex';

    giftModal.querySelector('.modal-close').addEventListener('click', closePopout);
}

async function sendGift(gameId) {
    //const giftUser = document.getElementById("giftUser").value.trim();
    const giftUser = "aaa";

    if (!giftUser) {
        alert("Por favor, ingresa el nombre de usuario.");
        return;
    }

    try {
        const response = await fetch('/Games-r-us/public/index.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                accion: 'regalarJuego',
                game_id: gameId,
                gift_user: giftUser
            }),
        });

        // Intentar interpretar la respuesta como JSON
        const textResponse = await response.text();
        try {
            const result = JSON.parse(textResponse);

            if (result.success) {
                // alert("Redirigiendo a la página de pago para el regalo...");
            } else {
                alert("Error al regalar el juego: " + result.message);
            }
        } catch (error) {
            // Si la respuesta no es JSON, asumir que es HTML de la vista de compra
            document.body.innerHTML = textResponse; // Cargar directamente la vista devuelta
        }
    } catch (error) {
        console.error("Error al enviar el regalo:", error);
        alert("Hubo un problema al procesar el regalo.");
    }
}

function closePopout() {
    document.querySelectorAll('.gift-modal-container').forEach(modal => modal.remove());
    document.querySelectorAll('.modal-container').forEach(modal => modal.remove());
}

document.addEventListener("DOMContentLoaded", function() {
    fetch("/Games-r-us/src/controladores/ajax.php?action=getUltimasInserciones")
        .then(response => {
            console.log("Response:", response);
            return response.json(); // Convertir a JSON correctamente
        })
        .then(data => {
            console.log("Datos recibidos:", data); // Verificar que llega el JSON correcto
            let lista = document.getElementById("lista-juegos-ajax");
            lista.innerHTML = ""; // Limpiar lista
            data.forEach(juego => {
                let item = document.createElement("li");
                item.textContent = `Nombre: ${juego.titulo}}`;
                lista.appendChild(item);
            });
        })
        .catch(error => console.error("Error en la petición AJAX:", error));
});

