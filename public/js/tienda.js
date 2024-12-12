async function fetchData() {
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
            renderStore(result.data);
            renderSidebar(result.data);
        } else {
            console.error("Error en el servidor:", result.message);
        }
    } catch (error) {
        console.error("Error al realizar la solicitud:", error);
    }
}

function renderStore(data, filterCategory = null) {
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
        const productElement = document.createElement('div');
        productElement.classList.add('product');
        productElement.innerHTML = `
            <img src="${product.image}" alt="${product.title}">
            <div class="divider"></div>
            <div class="product-content">
                <h3>${product.title}</h3>
                <p>${product.description}</p>
                <p><strong>Precio:</strong> ${product.precio}€</p>
                <div class="buttons">
                    <form method="POST">
                        <input type="text" name="compra_gameid" value="${product.id}" hidden>
                        <input type="submit" class="play-input" name="tienda_comprar" value="Comprar">
                        <input type="submit" name="tienda_regalar" value="Regalar">
                    </form>
                </div>
            </div>
        `;

        // Evento para abrir el modal
        productElement.addEventListener('click', () => {
            openModal(product);
        });

        categoryContainer.appendChild(productElement);
    });

    storeContainer.appendChild(categoryContainer);
}

function openModal(product) {
    const modalContainer = document.createElement('div');
    modalContainer.classList.add('modal-container');
    modalContainer.innerHTML = `
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <h3>${product.title}</h3>
            <img src="${product.image}" alt="${product.title}">
            <p>${product.description}</p>
            <p><strong>Precio:</strong> ${product.precio}€</p>
            <div class="buttons">
                <form method="POST">
                    <input type="text" name="compra_gameid" value="${product.id}" hidden>
                    <input type="submit" class="play-input" name="tienda_comprar" value="Comprar">
                    <input type="submit" name="tienda_regalar" value="Regalar">
                </form>
            </div>
        </div>
    `;

    document.body.appendChild(modalContainer);

    // Mostrar el modal
    modalContainer.style.display = 'flex';

    // Evento para cerrar el modal
    modalContainer.querySelector('.modal-close').addEventListener('click', () => {
        modalContainer.style.display = 'none';
        modalContainer.remove();
    });

    // Cerrar el modal al hacer clic fuera del contenido
    modalContainer.addEventListener('click', (event) => {
        if (event.target === modalContainer) {
            modalContainer.style.display = 'none';
            modalContainer.remove();
        }
    });
}

function renderSidebar(data) {
    const sidebar = document.getElementById('sidebar');
    const categoryList = document.getElementById('category-list');
    categoryList.innerHTML = ''; // Limpiar categorías previas

    const categories = [...new Set(data.map(product => product.categoria))];

    const allCategoriesItem = document.createElement('li');
    allCategoriesItem.textContent = 'Todas las categorías';
    allCategoriesItem.addEventListener('click', () => renderStore(data));
    categoryList.appendChild(allCategoriesItem);

    categories.forEach(category => {
        const categoryItem = document.createElement('li');
        categoryItem.textContent = category;
        categoryItem.addEventListener('click', () => renderStore(data, category));
        categoryList.appendChild(categoryItem);
    });
}

// Llamar a la función al cargar la página
document.addEventListener('DOMContentLoaded', fetchData);
