var IP = "192.168.1.48";



// Función para mostrar una alerta temporal
function mostrarAlerta(tipo, mensaje) {
    const alertElement = document.createElement('div');
    alertElement.classList.add('alert', `alert-${tipo}`, 'mt-3');
    alertElement.textContent = mensaje;
    alertContainer.appendChild(alertElement);

    setTimeout(function () {
        alertContainer.removeChild(alertElement);
    }, 3000);
}



// Configurar la URL de la solicitud
//var url = `http://${IP}/php_api_dislexia/save_score.php`;
var url = `http://localhost/php_api_dislexia/save_score.php`;
//var url = `http://localhost/php-dislexia/save_score.php`;

// Función para enviar la puntuación al servidor
function guardarScore(idEstudiante, curso, unidad, juego, puntaje) {

    var datos = {
        id_estudiante: idEstudiante,
        curso: curso,
        unidad: unidad,
        juego: juego,
        puntaje: puntaje
    };

    var opciones = {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(datos)
    };

    fetch(url, opciones)
        .then(response => response.json())
        .then(data => {
            console.log(dat);
            // console.log(data);
            if (data.codResponse === '00') {
                /* $("#modalPuntajeBody").text(`Tu puntaje es: ${puntaje} puntos`);
                $("#modalPuntaje").modal("show"); */
                mostrarAlerta('success', data.msjResponse);
            } else {
                mostrarAlerta('danger', data.msjResponse);
            }
        })
        .catch(error => {
            mostrarAlerta('danger', 'Error al enviar la solicitud al servidor');
        });
}


// Función para recargar la página
function reload() {
    location.reload();
}

const btnNuevo = document.getElementById('btn_nuevo');
btnNuevo.addEventListener('click', reload);