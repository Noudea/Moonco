document.addEventListener('DOMContentLoaded', (event) => {
    //the event occurred
    const nameInput = document.querySelector("#movie_name")
    const autoload = document.getElementById("autoload")
    const synopsis = document.getElementById("movie_sypnosis")
    const picture = document.getElementById('movie_picture')
    const formdiv = document.getElementById('imgContainer')
    const img = document.querySelector("#imgContainer > img")
    const suggestions = document.getElementById('suggestion')
    const movie_categoryInput = document.getElementById('movie_categoryInput')
    const category =  []


    var myHeaders = new Headers();
    myHeaders.append("Authorization", "Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI5MDk4OGMzYmI2Mzk4Nzc1MzEyY2E5YjcwZDBlNGMzOSIsInN1YiI6IjYwMmZjNWE2MDk3YzQ5MDAzZWZlZWE2NCIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.il1dYHYbmJxANZPELwzx8Mfpy5MWwChBvanVd5yyJWM");
    
    var requestOptions = {
        method: 'GET',
        headers: myHeaders,
        redirect: 'follow'
        };
        
    fetch("https://api.themoviedb.org/3/genre/movie/list?&language=fr-FR", requestOptions)
        .then(response => response.json())
        .then(response => {
            response.genres.forEach(genre => {
                category[genre.id] = genre.name
            });
        })
        .catch(error => console.log('error', error));

    console.log('category ',category)
    autoload.addEventListener('click' , () => {
        let movieName = encodeURIComponent(nameInput.value.trim())
        fetch("https://api.themoviedb.org/3/search/movie?language=fr-FR&query="+movieName+"&page=1&include_adult=false", requestOptions)
            .then(response => response.json())
            .then((response) => {
                
                console.log('reponse : ',response)
                /**
                 * afficher les suggestions
                 */
                if(Array.from(suggestion.children).length == 0) {
                    response.results.forEach(movie => {
                        let div = document.createElement('div')
                        suggestion.append(div)
                        let p = document.createElement('P')
                        div.append(p)
                        p.innerText = movie.title
                    });
                } else {
                    while (suggestion.hasChildNodes()) {
                    suggestion.removeChild(suggestion.lastChild);
                }
                    response.results.forEach(movie => {
                        let div = document.createElement('div')
                        suggestion.append(div)
                        let p = document.createElement('P')
                        div.append(p)
                        p.innerText = movie.title
                    });
                }
                
                /**
                 * evenement sur les suggestions
                 */
                for (let index = 0; index < Array.from(suggestion.children).length; index++) {
                    const element = Array.from(suggestion.children)[index];
                    /**
                     * mettre couleur de fond en hover
                     */
                    element.addEventListener('mouseover',()=> {
                        element.style.backgroundColor = 'red'
                        element.style.cursor = "pointer";
                    })
                    element.addEventListener('mouseleave',()=> {
                        element.style.backgroundColor = 'white'
                    })
                    /**
                     * sur le click remplir les champs de formulaire
                     */
                    element.addEventListener('click',()=> {
                        nameInput.value = response.results[index].title
                        synopsis.value = response.results[index].overview
                        picture.value = 'https://image.tmdb.org/t/p/original'+response.results[index].poster_path
                        img.src = 'https://image.tmdb.org/t/p/original'+response.results[index].poster_path;
                        
                        let genres = response.results[index].genre_ids
                        let genreArray = []
                        genres.forEach(genre => {
                            console.log(category[genre])
                            genreArray.push(category[genre])
                        });
                        console.log(genreArray)
                        movie_categoryInput.value = genreArray.join(' ')
                    })
                }
            })    
            .catch(error => console.log('error', error));
    })


    // console.log(window.Dropzone)
    // const Dropzone = window.Dropzone

    // Dropzone.options.myAwesomeDropzone = {
    // paramName: "file", // The name that will be used to transfer the file
    // maxFilesize: 2, // MB
    // accept: function(file, done) {
    //     if (file.name == "justinbieber.jpg") {
    //     done("Naha, you don't.");
    //     }
    //         else { done(); }
    //     }
    // };


    /**
     * AJOUTER form data
     */
    // const formData = new FormData()
    // formData.append('blob', new Blob(['Hello World!\n']), 'test')

    // fetch('http://localhost:8080/server/17/addMovie', {
    // method: 'POST',
    // body: formData
    // })
    // .then(r => r.json())
    // .then(data => {
    // console.log(data)
    // })


    document.getElementById('movie').addEventListener('submit', onSubmit);

function onSubmit(event) {
    event.preventDefault();

    var formData = new FormData();
    formData.append("movie[link]", document.getElementById("movie_link").files[0]);
    console.log( document.getElementById("movie_link").files[0])
    formData.append("movie[name]", document.getElementById("movie_name").value);
    formData.append("movie[categoryInput]", document.getElementById("movie_categoryInput").value);
    formData.append("movie[sypnosis]", document.getElementById("movie_sypnosis").value);
    formData.append("movie[picture]", document.getElementById("movie_picture").value);

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "http://localhost:8080/server/17/addMovie");
    // xhr.addEventListener('load', onRequestComplete, false);
    // xhr.upload.addEventListener("load", onUploadComplete, false);
    xhr.upload.addEventListener("progress", onUploadProgress, false);
    xhr.send(formData);
}

function onUploadProgress(event) {
    if (event.lengthComputable) {
        var percentComplete = event.loaded / event.total;
        document.getElementById('progress-value').textContent = parseFloat(percentComplete*100).toFixed(2);
    }
}

// // document.getElementById('upload-form').addEventListener('submit', onSubmit);
// document.getElementById('movie').addEventListener('submit', onSubmit);

// function onSubmit(event) {
//     event.preventDefault();

//     var formData = new FormData();
//     formData.append("upload[file]", document.getElementById("file").files[0]);
//     // formData.append("upload[name]", document.getElementById("name").value);

//     var xhr = new XMLHttpRequest();
//     xhr.open("POST", "http://localhost:8080/server/17/addMovie");
//     // xhr.addEventListener('load', onRequestComplete, false);
//     // xhr.upload.addEventListener("load", onUploadComplete, false);
//     xhr.upload.addEventListener("progress", onUploadProgress, false);
//     xhr.send(formData);
// }

// function onUploadProgress(event) {
//     if (event.lengthComputable) {
//         var percentComplete = event.loaded / event.total;
//         document.getElementById('progress-value').textContent = parseFloat(percentComplete*100).toFixed(2);
//     }
// }




})




