const likeButtons = document.querySelectorAll('.likeButton');
likeButtons.forEach(likeButton => {
    likeButton.addEventListener('click', (e) => {
        e.preventDefault();
        const idPost = likeButton.dataset.idpost;
        fetch('/favorite/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                idPost: idPost
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success){
                if(likeButton.classList.contains('like')){
                    likeButton.classList.add('hidden');
                    document.querySelector(`.noLike[data-idPost="${idPost}"]`).classList.remove('hidden');
                }else{
                    likeButton.classList.add('hidden');
                    document.querySelector(`.like[data-idPost="${idPost}"]`).classList.remove('hidden');
                }
            }
        })
    })
});