var IP = "192.168.100.185";



var mensajeIntentarNuevamente = "¡Casi lo tienes! No todas las respuestas están correctas, pero no te rindas. ¡Sigue intentándolo y mejorarás!";
var mensajeOk = "¡Felicitaciones! ¡Respuestas correctas!";

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
var url = `http://${IP}/php_api_dislexia/save_score.php`;
//var url = `http://localhost/php_api_dislexia/save_score.php`;
//var url = `http://localhost/php-dislexia/save_score.php`;

//var url = "../../../save_score.php";


// Función para enviar la puntuación al servidor
function guardarScore(idEstudiante, curso, unidad, juego, puntaje) {
    console.log("solicitud a" + IP);
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
            console.error('danger', 'Error al enviar la solicitud al servidor');
            //mostrarAlerta('danger', 'Error al enviar la solicitud al servidor');
        });
}


// Función para recargar la página
function reload() {
    location.reload();
}

try {
    const btnNuevo = document.getElementById('btn_nuevo');
    btnNuevo.addEventListener('click', reload);
} catch (error) {
    console.log(error)
}







//======================================================================================
var URL_ = `http://${IP}/php_api_dislexia/`;
var cursos = "lc_cursos.php";
var estudiantes = "lc_estudiantes.php";
var actividades = "lc_actividades.php";


const coursesData = {
    '2': {
        name: '5to Grado',
        description: 'Descripción del 5to Grado.'
    },
    '7': {
        name: '6to Grado',
        description: 'Descripción del 6to Grado.'
    },
    '8': {
        name: '7mo Grado',
        description: 'Descripción del 7mo Grado.'
    }
};

let selectedCourse = '';

function abrirListaEstudiantes(courseId) {
    console.log("==========================");
    console.log("consulta de estudiantes");
    selectedCourse = courseId;
    let course = coursesData[courseId];

    // Ocultar sección de cursos y mostrar sección de estudiantes
    document.getElementById("studentsSection").style.display = "block";
    document.getElementById("activitiesSection").style.display = "none";
    document.getElementById("unitsSection").style.display = "none";
    document.getElementById("activitiesSection2").style.display = "none";


    // Mostrar el nombre del curso y cargar la lista de estudiantes
    document.getElementById("studentsSection").innerHTML = `
    <h1>${course.name}</h1>
    <button class="btn btn-secondary mb-3" onclick="abrirListaCursos()">Volver a la Lista de Cursos</button>
    <ul class="list-group" id="studentList">
     
    </ul >
  `;

    const studentList = document.getElementById("studentList");
    studentList.innerHTML = '';

    var opciones = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    };

    // Cargar y mostrar la lista de estudiantes
    // Cargar y mostrar la lista de estudiantes desde el servidor
    const url = `${URL_}${estudiantes}?curso_id=${courseId}`;

    fetch(url, opciones)
        .then(response => response.json())
        .then(studentsInCourse => {
            studentsInCourse.forEach(student => {
                console.log("datos de estudiantes. " + student);
                const studentItem = document.createElement("li");
                studentItem.className = "list-group-item";
                studentItem.textContent = student.NOMBRES;
                //funcionalidad anterior abrir actividades
                //studentItem.addEventListener("click", () => showActivities(student.ID));
                studentItem.addEventListener("click", () => abrirUnidades(student.ID));
                studentList.appendChild(studentItem);
            });
        })
        .catch(error => {
            console.error("se presentó el siguiente error:", error);
            console.error("Error al cargar la lista de estudiantes:", error);
        });


    /*   const studentsInCourse = [
          { id: 1, name: "Estudiante 1" },
          { id: 2, name: "Estudiante 2" },
          // ... agregar más estudiantes
      ];
  
      studentsInCourse.forEach(student => {
          const studentItem = document.createElement("li");
          studentItem.className = "list-group-item";
          studentItem.textContent = student.name;
          studentItem.addEventListener("click", () => showActivities(student.id));
          studentList.appendChild(studentItem);
      }); */

    console.log("fin de consulta de estudiantes");

    console.log("inicio forma anterior");
    //guardarScore(2, 2, 2, 2, 2) 
    console.log("=====> fin forma anterior");
}

// Función para mostrar actividades de un estudiante
function showActivities(studentId, actividadId, idUnidad) {

    // Ocultar sección de estudiantes y mostrar sección de actividades y puntajes
    document.getElementById("studentsSection").style.display = "none";
    document.getElementById("activitiesSection").style.display = "block";


    document.getElementById("unitsSection").style.display = "none";
    document.getElementById("activitiesSection2").style.display = "none";
    document.getElementById("unitsSection").style.display = "none";



    // Mostrar la lista de actividades y puntajes del estudiante seleccionado
    document.getElementById("activitiesSection").innerHTML = `
    <h1>Actividades y Puntajes</h1>
    <button class="btn btn-secondary mb-3" onclick="abrirActividades(selectedUnit)">Volver a la Lista de Actividades</button>
    <ul class="list-group" id="activityList">

    </ul>
  `;

    const activityList = document.getElementById("activityList");
    activityList.innerHTML = '';



    //const url = `${URL_}${actividades}?estudiante_id=${studentId}&actividadId=${actividadId}&idUnidad=${idUnidad}`;
    const url = `${URL_}${actividades}?estudiante_id=${studentId}&actividadId=${actividadId}`;
    fetch(url)
        .then(response => response.json())
        .then(studentActivities => {
            studentActivities.forEach(activity => {
                const activityItem = document.createElement("li");
                activityItem.className = "list-group-item";
                activityItem.textContent = `${activity.unidad} - ${activity.juego} - Puntaje: ${activity.puntaje}`;
                activityList.appendChild(activityItem);
            });
        })
        .catch(error => {
            console.error("Error al cargar la lista de actividades:", error);
        });
}

// Función para volver a la lista de cursos
function abrirListaCursos() {
    selectedCourse = '';
    document.getElementById("studentsSection").style.display = "none";
    document.getElementById("activitiesSection").style.display = "none";
    document.getElementById("activitiesSection2").style.display = "none";
}



var idEstudiante;
//new

function abrirUnidades(selectedStudent) {
    idEstudiante = selectedStudent;
    document.getElementById("unitsSection").style.display = "block";
    document.getElementById("activitiesSection").style.display = "none";
    document.getElementById("activitiesSection2").style.display = "none";

    document.getElementById("studentsSection").style.display = "none";

    /*   // Mostrar la lista de unidades del curso
      document.getElementById("unitList").innerHTML = `
      <li class="list-group-item" onclick="showActivities(selectedStudent, '1')">Unidad 1</li>
      <li class="list-group-item" onclick="showActivities(selectedStudent, '2')">Unidad 2</li>
      <li class="list-group-item" onclick="showActivities(selectedStudent, '3')">Unidad 3</li>
      <li class="list-group-item" onclick="showActivities(selectedStudent, '4')">Unidad 4</li>
    `; */
}


// Función para mostrar actividades de una unidad
function abrirActividades(unitId) {
    selectedUnit = unitId;
    document.getElementById("unitsSection").style.display = "none";
    document.getElementById("activitiesSection2").style.display = "block";

    document.getElementById("unitsSection").style.display = "none";
    document.getElementById("studentsSection").style.display = "none";
    document.getElementById("activitiesSection").style.display = "none";


    // Mostrar la lista de actividades de la unidad seleccionada
    /*document.getElementById("activityList2").innerHTML = `
    <h1>Actividades de la Unidad ${unitId}</h1>
    <button class="btn btn-secondary mb-3" onclick="showUnits()">Volver a la Lista de Unidades</button>
    <ul class="list-group" id="activityList2">
      <li class="list-group-item" onclick="showScores(selectedStudent, selectedUnit, '1')">Actividad 1</li>
      <li class="list-group-item" onclick="showScores(selectedStudent, selectedUnit, '2')">Actividad 2</li>
      <li class="list-group-item" onclick="showScores(selectedStudent, selectedUnit, '3')">Actividad 3</li>
      <li class="list-group-item" onclick="showScores(selectedStudent, selectedUnit, '4')">Actividad 4</li>
    </ul>
    `;*/
}



// Función para mostrar puntajes de una actividad
function showScores(activityId) {

    console.log({ idEstudiante });
    console.log({ selectedUnit });
    console.log({ activityId });

    showActivities(idEstudiante, selectedUnit);

    /*document.getElementById("activitiesSection").style.display = "none";
    document.getElementById("scoresSection").style.display = "block";

    // Mostrar la lista de puntajes de la actividad seleccionada
    document.getElementById("scoreList").innerHTML = `
    <h1>Puntajes de la Actividad ${activityId}</h1>
    <button class="btn btn-secondary mb-3" onclick="showActivities(selectedStudent, selectedUnit)">Volver a la Lista de Actividades</button>
    <ul class="list-group" id="scoreList">
      <li class="list-group-item">Puntaje 1</li>
      <li class="list-group-item">Puntaje 2</li>
      <li class="list-group-item">Puntaje 3</li>
      <li class="list-group-item">Puntaje 4</li>
    </ul>
    `;
    */
}