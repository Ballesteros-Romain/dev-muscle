let links = document.querySelectorAll("[data-delete]");
// on boucle sur les liens

for (let link of links) {
  // on met les ecouteurs d'evenement
  link.addEventListener("click", function (e) {
    // on empeche la navigation
    e.preventDefault();
    //on demande confirmation
    if (confirm("Voulez vous supprimer cette image ?", link)) {
      // on envoie la requete
      fetch(this.getAttribute("href"), {
        method: "DELETE",
        headers: {
          "X-Requested-with": "XMLHttpRequest",
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ _token: this.dataset.token }),
        // body: JSON.stringify({ delete_image: this.dataset.token }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.succes) {
            this.parentElement.remove();
          } else {
            alert(data.error);
          }
        });
    }
  });
}
