let result = []; // Cambiar a let para permitir reasignación
let nickUser = "";

async function fetchData(nick) {
    nickUser = nick;
    try {
        const response = await fetch('/Games-r-us/public/index.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({ accion: 'listarcesta', nick: nick }),
        });

        if (!response.ok) {
            throw new Error("Error al obtener los datos");
        }

        const data = await response.json();
        if (data.success) {
            result = data.message; // Asignar los datos a result
            listarcesta();
        } else {
            console.error("Error en el servidor:", data.message);
            alert("Error al cargar el carrito. Inténtalo de nuevo.");
        }
    } catch (error) {
        console.error("Error al realizar la solicitud:", error);
        alert("Hubo un problema al cargar el carrito. Inténtalo de nuevo.");
    }
}

function actualizarTotalProductos() {
    const totalProductos = result.length; // Cuenta los productos en la lista
    document.getElementById('totalProductos').textContent = totalProductos;
}

function actualizarPrecioTotal() {
    const precioTotal = result.reduce((total, producto) => total + parseFloat(producto.precio), 0);
    document.getElementById('precioTotal').textContent = `${precioTotal.toFixed(2)}€`; // Formato con dos decimales
}

// Modificar `listarcesta` para actualizar estos valores
function listarcesta() {
    const padre = document.getElementById('padre');

    if (result.length === 0) {
        console.log("No hay productos en el carrito");
        return;
    }

    const categoryContainer = document.createElement('div');
    categoryContainer.id = 'container';
    padre.appendChild(categoryContainer);
    categoryContainer.innerHTML = ''; // Limpiar antes de agregar nuevos elementos

    var url = "../src/uploads/image/portadas/";

    result.forEach(miniresult => {
        console.log(miniresult);
        const productElement = document.createElement('div');
        productElement.classList.add('product');
        productElement.innerHTML = `
            <img src="${url + miniresult.ruta_imagen}" alt="${miniresult.titulo}">
            <div class="divider"></div>
            <div class="product-content">
                <h3>${miniresult.titulo}</h3>
                <p><strong>Desarrollador:</strong> ${miniresult.desarrollador}</p>
                <p><strong>Año:</strong> ${miniresult.anio}</p>
                <p><strong>Precio:</strong> ${miniresult.precio}€</p>           
                <button type="submit" class="btn btn-danger" onclick="EliminarProducto(${miniresult.id_juego})">Eliminar</button>    
            </div>
        `;
        categoryContainer.appendChild(productElement);
    });

    // Llamar a las funciones para actualizar los totales
    actualizarTotalProductos();
    actualizarPrecioTotal();
}

async function EliminarProducto(id) {
    const nick = nickUser;
    try {
        
        if (!id || !nick) {
            console.error("Faltan datos para eliminar el juego de la cesta.");
            return;
        }

        const response = await fetch('/Games-r-us/public/index.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({ accion: 'eliminarJuegoCesta', id_juego: id, nick: nick }),
        });

        if (!response.ok) {
            throw new Error("Error al eliminar el juego de la cesta");
        }

        const data = await response.json();
        if (data.success) {
            console.log("Juego eliminado con éxito:", data.message);
            // Puedes eliminar el elemento del DOM o recargar la página si es necesario.
            window.location.reload();
        } else {
            console.error("Error en el servidor:", data.message);
        }
    } catch (error) {
        console.error("Error al realizar la solicitud:", error);
    }
}

async function vaciarCarrito() {
    try {
        const response = await fetch('/Games-r-us/public/index.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                accion: 'vaciarcesta',
                nick: nickUser
            }),
        });

        if (!response.ok) {
            throw new Error("Error al vaciar el carrito");
        }

        const data = await response.json();
        if (data.success) {
            console.log("Carrito vaciado correctamente:", data.message);

            // Vaciar la variable local result
            result = [];

            // Limpiar la UI del carrito
            document.getElementById('container').innerHTML = '';

            // Actualizar totales
            actualizarTotalProductos();
            actualizarPrecioTotal();
        } else {
            console.error("Error en el servidor:", data.message);
        }
    } catch (error) {
        console.error("Error al realizar la solicitud:", error);
    }
}
