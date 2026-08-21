document.addEventListener("DOMContentLoaded", () => {
  const buttons = document.querySelectorAll(".tipo-btn");
  const tipo = document.getElementById("tipo");
  const naturezaGrupo = document.getElementById("natureza-grupo");
  const status = document.getElementById("status");
  buttons.forEach((button) => button.addEventListener("click", () => {
    buttons.forEach((item) => item.classList.remove("active")); button.classList.add("active"); tipo.value = button.dataset.tipo;
    const saida = tipo.value === "saida"; naturezaGrupo.hidden = !saida;
    status.innerHTML = saida ? '<option value="pago">Pago</option><option value="pendente">Pendente</option>' : '<option value="recebido">Recebido</option><option value="pendente">Pendente</option>';
  }));
});
