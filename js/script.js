document.addEventListener("DOMContentLoaded", () => {
  const buttons = document.querySelectorAll(".tipo-btn");

  buttons.forEach((btn) => {
    btn.addEventListener("click", () => {
      buttons.forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");
    });
  });

  document.getElementById("form").addEventListener("submit", (e) => {
    e.preventDefault();
    alert("Transação salva!");
  });
});