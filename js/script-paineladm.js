const tabs = document.querySelectorAll(".tab");
const contents = document.querySelectorAll(".tab-content");

tabs.forEach(tab => {
  tab.addEventListener("click", () => {
    
    // remove ativo de tudo
    tabs.forEach(t => t.classList.remove("active"));
    contents.forEach(c => c.classList.remove("active"));

    // ativa o clicado
    tab.classList.add("active");

    const id = tab.getAttribute("data-tab");
    document.getElementById(id).classList.add("active");
  });
});