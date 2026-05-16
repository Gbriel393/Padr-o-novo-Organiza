function togglePassword() {
  const input = document.getElementById("senha");

  if (input.type === "password") {
    input.type = "text";
  } else {
    input.type = "password";
  }
}

document.getElementById("loginForm").addEventListener("submit", function(e) {
  e.preventDefault();

  const email = document.getElementById("email").value;
  const senha = document.getElementById("senha").value;

  if (!email || !senha) {
    alert("Preencha todos os campos!");
  } else {
    alert("Login realizado!");
  }
});