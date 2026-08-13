document.addEventListener("DOMContentLoaded", function () {

    // ==========================================
    // DADOS DO GRÁFICO
    // ==========================================

    const dados = [
        {
            mes: "Jan",
            receitas: 4200,
            despesas: 1800
        },
        {
            mes: "Fev",
            receitas: 3000,
            despesas: 2500
        },
        {
            mes: "Mar",
            receitas: 1900,
            despesas: 3200
        },
        {
            mes: "Abr",
            receitas: 2780,
            despesas: 3908
        },
        {
            mes: "Mai",
            receitas: 1800,
            despesas: 2900
        },
        {
            mes: "Jun",
            receitas: 2400,
            despesas: 3100
        }
    ];


    // ==========================================
    // CONFIGURAÇÕES
    // ==========================================

    const chart = document.getElementById("lineChart");
    const tooltip = document.getElementById("lineTooltip");

    const largura = 600;
    const altura = 220;

    const valorMaximo = 6000;


    // ==========================================
    // CALCULA A POSIÇÃO DOS PONTOS
    // ==========================================

    function calcularPontos() {

        const espacamento = largura / (dados.length - 1);

        return dados.map((item, index) => {

            const x = index * espacamento;

            const valor = item.receitas;

            const y = altura - ((valor / valorMaximo) * altura);

            return {
                ...item,
                x: x,
                y: y
            };

        });

    }


    // ==========================================
    // CRIA CURVA SUAVE
    // ==========================================

    function criarCurva(pontos) {

        if (pontos.length === 0) {
            return "";
        }

        let path = `M ${pontos[0].x} ${pontos[0].y}`;

        for (let i = 0; i < pontos.length - 1; i++) {

            const pontoAtual = pontos[i];
            const proximoPonto = pontos[i + 1];

            const meioX =
                (pontoAtual.x + proximoPonto.x) / 2;

            path += `
                C
                ${meioX} ${pontoAtual.y},
                ${meioX} ${proximoPonto.y},
                ${proximoPonto.x} ${proximoPonto.y}
            `;
        }

        return path;
    }


    // ==========================================
    // DESENHA O GRÁFICO
    // ==========================================

    function desenharGrafico() {

        chart.innerHTML = "";

        const pontos = calcularPontos();

        const pathData = criarCurva(pontos);


        // --------------------------------------
        // LINHA
        // --------------------------------------

        const path = document.createElementNS(
            "http://www.w3.org/2000/svg",
            "path"
        );

        path.setAttribute("d", pathData);

        chart.appendChild(path);


        // --------------------------------------
        // PONTOS
        // --------------------------------------

        pontos.forEach((ponto, index) => {

            const circle = document.createElementNS(
                "http://www.w3.org/2000/svg",
                "circle"
            );

            circle.setAttribute("cx", ponto.x);
            circle.setAttribute("cy", ponto.y);
            circle.setAttribute("r", "4");

            circle.classList.add("chart-circle");

            chart.appendChild(circle);


            // ----------------------------------
            // MOUSE ENTRANDO
            // ----------------------------------

            circle.addEventListener("mouseenter", function (event) {

                mostrarTooltip(
                    ponto,
                    event
                );

            });


            // ----------------------------------
            // MOUSE SAINDO
            // ----------------------------------

            circle.addEventListener("mouseleave", function () {

                esconderTooltip();

            });


            // ----------------------------------
            // MOUSE MOVENDO
            // ----------------------------------

            circle.addEventListener("mousemove", function (event) {

                posicionarTooltip(
                    event
                );

            });

        });

    }


    // ==========================================
    // MOSTRAR TOOLTIP
    // ==========================================

    function mostrarTooltip(ponto, event) {

        tooltip.innerHTML = `
            <strong>${ponto.mes}</strong>

            <span class="receita-text">
                receitas : ${ponto.receitas}
            </span>

            <span class="despesa-text">
                despesas : ${ponto.despesas}
            </span>
        `;

        tooltip.classList.add("active");

        posicionarTooltip(event);

    }


    // ==========================================
    // POSICIONAR TOOLTIP
    // ==========================================

    function posicionarTooltip(event) {

        const graph = document.querySelector(".graph");

        const rect = graph.getBoundingClientRect();

        let x = event.clientX - rect.left + 15;
        let y = event.clientY - rect.top - 20;


        // Não deixar sair pela direita

        if (x + tooltip.offsetWidth > rect.width) {

            x = event.clientX - rect.left
                - tooltip.offsetWidth
                - 15;

        }


        // Não deixar sair por cima

        if (y < 0) {

            y = event.clientY - rect.top + 15;

        }


        tooltip.style.left = `${x}px`;
        tooltip.style.top = `${y}px`;

    }


    // ==========================================
    // ESCONDER TOOLTIP
    // ==========================================

    function esconderTooltip() {

        tooltip.classList.remove("active");

    }


    // ==========================================
    // INICIAR
    // ==========================================

    desenharGrafico();


    // ==========================================
    // REDESENHAR QUANDO A TELA MUDA
    // ==========================================

    window.addEventListener("resize", function () {

        desenharGrafico();

    });

});