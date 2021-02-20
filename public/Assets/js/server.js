
document.addEventListener('DOMContentLoaded', (event) => {
    const movieWrapper = Array.from(document.getElementsByClassName('movieWrapper'))
    const imgWrapper = Array.from(document.getElementsByClassName('movieWrapper'))
    const videoPreview = Array.from(document.getElementsByClassName('videoPreview'))
    const moviePreview = Array.from(document.getElementsByClassName('moviePreview'))

    // movieWrapper.forEach(movie => {
    //     movie.addEventListener('mouseenter', () => {
    //         console.log(movie.childNodes[1].childNodes)
    //         // movie.style.transform = "translate(50px, 100px),scale3d(1.2, 1.2,1 )"
    //         movie.style.transform = "scale3d(1.2, 1.2,1 )"
    //     })
    //     movie.addEventListener('mouseleave', () => {
    //         // movie.style.transform = "translate(-20px, -50px)"
    //         movie.style.transform = "scale3d(1, 1,1 )"
    //     })
    // });

    for (let index = 0; index < movieWrapper.length; index++) {
        const movie = movieWrapper[index];
         movie.addEventListener('mouseenter', () => {
            console.log(videoPreview[index])
            movie.style.zIndex = "1"
            moviePreview[index].classList.remove('none')
            // movie.style.transform = "translate(50px, 100px),scale3d(1.2, 1.2,1 )"
            movie.style.transform = "scale3d(1.2, 1.2,1 )"
            videoPreview[index].play()
        })
        movie.addEventListener('mouseleave', () => {
            // movie.style.transform = "translate(-20px, -50px)"
            movie.style.transform = "scale3d(1, 1,1 )"
            videoPreview[index].pause()
            moviePreview[index].classList.add('none')
            movie.style.zIndex = "0"
        })

        
    }

})
