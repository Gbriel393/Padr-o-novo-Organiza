document.addEventListener("DOMContentLoaded", function () {

    // ==========================================
    // MESES
    // ==========================================

    const meses = [
        "Jan",
        "Fev",
        "Mar",
        "Abr",
        "Mai",
        "Jun",
        "Jul",
        "Ago",
        "Set",
        "Out",
        "Nov",
        "Dez"
    ];


    // ==========================================
    // DADOS VINDOS DO PHP
    // ==========================================

    const dados = meses.map((mes, index) => {

        const numeroMes = index + 1;

        return {

            mes: mes,

            receitas: dados_grafico[numeroMes]
                ? Number(dados_grafico[numeroMes].receitas)
                : 0,

            despesas: dados_grafico[numeroMes]
                ? Number(dados_grafico[numeroMes].despesas)
                : 0

        };

    });


    // ==========================================
    // ELEMENTOS
    // ==========================================

    const chart = document.getElementById("lineChart");

    const tooltip =
        document.getElementById("lineTooltip");

    const monthsContainer =
        document.querySelector(".months");

    const barsContainer =
        document.querySelector(".bars");


    const prevButtons =
        document.querySelectorAll(".chart-prev");

    const nextButtons =
        document.querySelectorAll(".chart-next");


    // ==========================================
    // CONFIGURAÇÕES DO GRÁFICO
    // ==========================================

    const largura = 600;

    const altura = 220;

    const alturaBarras = 190;


    // ==========================================
    // CONTROLE DO CARROSSEL
    // ==========================================

    let pagina = 0;


    // Quantos meses aparecem de uma vez
    function quantidadeMeses() {

        if (window.innerWidth <= 600) {

            return 3;

        }

        return 6;

    }


    // ==========================================
    // QUANTIDADE DE PÁGINAS
    // ==========================================

    function quantidadePaginas() {

        return Math.ceil(
            dados.length / quantidadeMeses()
        );

    }


    // ==========================================
    // CORRIGIR PÁGINA
    // ==========================================

    function corrigirPagina() {

        const totalPaginas =
            quantidadePaginas();


        if (pagina >= totalPaginas) {

            pagina =
                totalPaginas - 1;

        }


        if (pagina < 0) {

            pagina = 0;

        }

    }


    // ==========================================
    // DADOS VISÍVEIS
    // ==========================================

    function dadosVisiveis() {

        const quantidade =
            quantidadeMeses();


        const inicio =
            pagina * quantidade;


        return dados.slice(
            inicio,
            inicio + quantidade
        );

    }


    // ==========================================
    // ESCALA DINÂMICA
    // ==========================================

    function calcularEscala() {

        let maiorValor = 0;


        dados.forEach(item => {

            maiorValor = Math.max(
                maiorValor,
                item.receitas,
                item.despesas
            );

        });


        if (maiorValor === 0) {

            return {
                maximo: 1000,
                passo: 250
            };

        }


        let maximo;


        if (maiorValor <= 1000) {

            maximo = 1000;

        } else if (maiorValor <= 2500) {

            maximo = 2500;

        } else if (maiorValor <= 5000) {

            maximo = 5000;

        } else if (maiorValor <= 10000) {

            maximo = 10000;

        } else if (maiorValor <= 15000) {

            maximo = 15000;

        } else if (maiorValor <= 20000) {

            maximo = 20000;

        } else {

            maximo =
                Math.ceil(
                    maiorValor / 5000
                ) * 5000;

        }


        return {

            maximo: maximo,

            passo: maximo / 4

        };

    }


    const escala =
        calcularEscala();


    const valorMaximo =
        escala.maximo;


    // ==========================================
    // ATUALIZAR EIXO Y
    // ==========================================

    function atualizarEscala() {

        const eixos =
            document.querySelectorAll(".y-axis");


        eixos.forEach(eixo => {

            const spans =
                eixo.querySelectorAll("span");


            spans.forEach((span, index) => {

                const valor =
                    valorMaximo -
                    (escala.passo * index);


                span.textContent =
                    valor.toLocaleString(
                        "pt-BR",
                        {
                            maximumFractionDigits: 0
                        }
                    );

            });

        });

    }


    // ==========================================
    // ATUALIZAR MESES
    // ==========================================

    function atualizarMeses() {

        if (!monthsContainer) {
            return;
        }


        monthsContainer.innerHTML = "";


        const visiveis =
            dadosVisiveis();


        visiveis.forEach(item => {

            const span =
                document.createElement("span");


            span.textContent =
                item.mes;


            monthsContainer.appendChild(
                span
            );

        });

    }


    // ==========================================
    // CALCULAR PONTOS
    // ==========================================

    function calcularPontos(tipo) {

        const visiveis =
            dadosVisiveis();


        if (visiveis.length === 1) {

            return [{
                ...visiveis[0],
                x: largura / 2,
                y:
                    altura -
                    (
                        (
                            visiveis[0][tipo] /
                            valorMaximo
                        ) * altura
                    )
            }];

        }


        const espacamento =
            largura /
            (visiveis.length - 1);


        return visiveis.map(
            (item, index) => {

                const x =
                    index * espacamento;


                const valor =
                    item[tipo];


                const y =
                    altura -
                    (
                        (
                            valor /
                            valorMaximo
                        ) * altura
                    );


                return {

                    ...item,

                    x: x,

                    y: y,

                    valor: valor

                };

            }
        );

    }


    // ==========================================
    // CRIAR CURVA
    // ==========================================

    function criarCurva(pontos) {

        if (pontos.length === 0) {

            return "";

        }


        if (pontos.length === 1) {

            return `
                M
                ${pontos[0].x}
                ${pontos[0].y}
            `;

        }


        let path =
            `M ${pontos[0].x} ${pontos[0].y}`;


        for (
            let i = 0;
            i < pontos.length - 1;
            i++
        ) {

            const atual =
                pontos[i];


            const proximo =
                pontos[i + 1];


            const meioX =
                (
                    atual.x +
                    proximo.x
                ) / 2;


            path += `
                C
                ${meioX} ${atual.y},
                ${meioX} ${proximo.y},
                ${proximo.x} ${proximo.y}
            `;

        }


        return path;

    }


    // ==========================================
    // CRIAR LINHA
    // ==========================================

    function criarLinha(
        pontos,
        tipo
    ) {

        const path =
            document.createElementNS(
                "http://www.w3.org/2000/svg",
                "path"
            );


        path.setAttribute(
            "d",
            criarCurva(pontos)
        );


        path.classList.add(
            tipo === "receitas"
                ? "linha-receitas"
                : "linha-despesas"
        );


        chart.appendChild(
            path
        );

    }


    // ==========================================
    // CRIAR PONTOS
    // ==========================================

    function criarPontos(
        pontos,
        tipo
    ) {

        pontos.forEach(ponto => {

            const circle =
                document.createElementNS(
                    "http://www.w3.org/2000/svg",
                    "circle"
                );


            circle.setAttribute(
                "cx",
                ponto.x
            );


            circle.setAttribute(
                "cy",
                ponto.y
            );


            circle.setAttribute(
                "r",
                "4"
            );


            circle.classList.add(
                "chart-circle"
            );


            circle.classList.add(
                tipo === "receitas"
                    ? "circle-receitas"
                    : "circle-despesas"
            );


            chart.appendChild(
                circle
            );


            circle.addEventListener(
                "mouseenter",
                function (event) {

                    mostrarTooltip(
                        ponto,
                        event
                    );

                }
            );


            circle.addEventListener(
                "mouseleave",
                function () {

                    esconderTooltip();

                }
            );


            circle.addEventListener(
                "mousemove",
                function (event) {

                    posicionarTooltip(
                        event
                    );

                }
            );

        });

    }


    // ==========================================
    // DESENHAR GRÁFICO DE LINHA
    // ==========================================

    function desenharGrafico() {

        if (!chart) {
            return;
        }


        chart.innerHTML = "";


        const pontosReceitas =
            calcularPontos("receitas");


        const pontosDespesas =
            calcularPontos("despesas");


        criarLinha(
            pontosReceitas,
            "receitas"
        );


        criarLinha(
            pontosDespesas,
            "despesas"
        );


        criarPontos(
            pontosReceitas,
            "receitas"
        );


        criarPontos(
            pontosDespesas,
            "despesas"
        );

    }


    // ==========================================
    // TOOLTIP
    // ==========================================

    function mostrarTooltip(
        ponto,
        event
    ) {

        tooltip.innerHTML = `

            <strong>
                ${ponto.mes}
            </strong>

            <span class="receita-text">

                receitas :
                R$ ${formatarMoeda(
                    ponto.receitas
                )}

            </span>

            <span class="despesa-text">

                despesas :
                R$ ${formatarMoeda(
                    ponto.despesas
                )}

            </span>

        `;


        tooltip.classList.add(
            "active"
        );


        posicionarTooltip(
            event
        );

    }


    // ==========================================
    // POSICIONAR TOOLTIP
    // ==========================================

    function posicionarTooltip(
        event
    ) {

        const graph =
            document.querySelector(
                ".chart-box:first-child .graph"
            );


        if (!graph) {
            return;
        }


        const rect =
            graph.getBoundingClientRect();


        let x =
            event.clientX -
            rect.left +
            15;


        let y =
            event.clientY -
            rect.top -
            20;


        if (
            x +
            tooltip.offsetWidth >
            rect.width
        ) {

            x =
                event.clientX -
                rect.left -
                tooltip.offsetWidth -
                15;

        }


        if (y < 0) {

            y =
                event.clientY -
                rect.top +
                15;

        }


        tooltip.style.left =
            `${x}px`;


        tooltip.style.top =
            `${y}px`;

    }


    // ==========================================
    // ESCONDER TOOLTIP
    // ==========================================

    function esconderTooltip() {

        tooltip.classList.remove(
            "active"
        );

    }


    // ==========================================
    // FORMATAR MOEDA
    // ==========================================

    function formatarMoeda(
        valor
    ) {

        return Number(valor).toLocaleString(
            "pt-BR",
            {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }
        );

    }


    // ==========================================
    // DESENHAR BARRAS
    // ==========================================

    function desenharBarras() {

        if (!barsContainer) {
            return;
        }


        barsContainer.innerHTML = "";


        const visiveis =
            dadosVisiveis();


        visiveis.forEach(item => {

            // ==============================
            // ITEM
            // ==============================

            const barItem =
                document.createElement(
                    "div"
                );


            barItem.classList.add(
                "bar-item"
            );


            // ==============================
            // CONTAINER
            // ==============================

            const barContainer =
                document.createElement(
                    "div"
                );


            barContainer.classList.add(
                "bar-container"
            );


            // ==============================
            // BARRA
            // ==============================

            const bar =
                document.createElement(
                    "div"
                );


            bar.classList.add(
                "bar"
            );


            const alturaBarra =
                (
                    item.receitas /
                    valorMaximo
                ) *
                alturaBarras;


            bar.style.height =
                `${alturaBarra}px`;


            // ==============================
            // TOOLTIP
            // ==============================

            const barTooltip =
                document.createElement(
                    "div"
                );


            barTooltip.classList.add(
                "bar-tooltip"
            );


            barTooltip.innerHTML = `

                <strong>
                    ${item.mes}
                </strong>

                <span class="receita-text">

                    receitas :
                    R$ ${formatarMoeda(
                        item.receitas
                    )}

                </span>

                <span class="despesa-text">

                    despesas :
                    R$ ${formatarMoeda(
                        item.despesas
                    )}

                </span>

            `;


            // ==============================
            // MONTAR
            // ==============================

            barContainer.appendChild(
                bar
            );


            barContainer.appendChild(
                barTooltip
            );


            barItem.appendChild(
                barContainer
            );


            // ==============================
            // MÊS
            // ==============================

            const mes =
                document.createElement(
                    "span"
                );


            mes.textContent =
                item.mes;


            barItem.appendChild(
                mes
            );


            barsContainer.appendChild(
                barItem
            );

        });

    }


    // ==========================================
    // ATUALIZAR SETAS
    // ==========================================

    function atualizarSetas() {

        corrigirPagina();


        const totalPaginas =
            quantidadePaginas();


        prevButtons.forEach(button => {

            button.disabled =
                pagina === 0;

        });


        nextButtons.forEach(button => {

            button.disabled =
                pagina >=
                totalPaginas - 1;

        });

    }


    // ==========================================
    // ATUALIZAR TUDO
    // ==========================================

    function atualizarGraficos() {

        corrigirPagina();

        atualizarEscala();

        atualizarMeses();

        desenharGrafico();

        desenharBarras();

        atualizarSetas();

    }


    // ==========================================
    // SETA ESQUERDA
    // ==========================================

    prevButtons.forEach(button => {

        button.addEventListener(
            "click",
            function () {

                if (pagina > 0) {

                    pagina--;

                    atualizarGraficos();

                }

            }
        );

    });


    // ==========================================
    // SETA DIREITA
    // ==========================================

    nextButtons.forEach(button => {

        button.addEventListener(
            "click",
            function () {

                if (
                    pagina <
                    quantidadePaginas() - 1
                ) {

                    pagina++;

                    atualizarGraficos();

                }

            }
        );

    });


    // ==========================================
    // INICIALIZAÇÃO
    // ==========================================

    atualizarGraficos();


    // ==========================================
    // RESPONSIVIDADE
    // ==========================================

    let quantidadeAnterior =
        quantidadeMeses();


    window.addEventListener(
        "resize",
        function () {

            const quantidadeAtual =
                quantidadeMeses();


            if (
                quantidadeAtual !==
                quantidadeAnterior
            ) {

                quantidadeAnterior =
                    quantidadeAtual;


                pagina = 0;

            }


            atualizarGraficos();

        }
    );

});