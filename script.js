let pessoas = [];

function adicionarPessoa() {
    const nome = document.getElementById("nomeInput").value;
    if (nome) {
        const id = pessoas.length + 1;
        pessoas.push({ id: id, nome: nome, filhos: [] });
        renderizarTabela();
        atualizarJsonOutput();
    }
}

function renderizarTabela() {
    const tbody = document.querySelector("#tabelaPessoas tbody");
    tbody.innerHTML = "";

    pessoas.forEach((pessoa, index) => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td>${pessoa.nome}</td>
            <td><button onclick="removerPessoa(${index})">Remover Pessoa</button></td>
            <td><button onclick="adicionarFilho(${index})">Adicionar Filho</button></td>
        `;
        tbody.appendChild(tr);

        pessoa.filhos.forEach((filho) => {
            const filhoTr = document.createElement("tr");
            filhoTr.innerHTML = `
                <td> - ${filho.nome}</td>
                <td><button onclick="removerFilho(${filho.id})">Remover Filho</button></td>
                <td></td>
            `;
            tbody.appendChild(filhoTr);
        });
    });
}

function adicionarFilho(index) {
    const dialog = document.createElement("dialog");
    dialog.innerHTML = `
        <p>Informe o nome do filho:</p>
        <input type="text" id="nomeFilhoInput">
        <button onclick="confirmarFilho(${index})">OK</button>
    `;
    document.body.appendChild(dialog);
    dialog.showModal();
}

function confirmarFilho(index) {
    const nomeFilho = document.getElementById("nomeFilhoInput").value;
    if (nomeFilho) {
        const idFilho = pessoas[index].filhos.length + 1;
        pessoas[index].filhos.push({ id: idFilho, nome: nomeFilho });
        renderizarTabela();
        atualizarJsonOutput();
    }
    const dialog = document.querySelector("dialog");
    dialog.close();
    dialog.remove();
}

function atualizarJsonOutput() {
    document.getElementById("jsonOutput").value = JSON.stringify(pessoas, null, 2);
}

function gravar() {
    const json = JSON.stringify(pessoas);
    function mostrarMensagem(mensagem) {
        alert(mensagem);
    }
    
    fetch('gravar.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: json
    })
    .then(response => {
        if (response.ok) {
            return response.text();
        }
        throw new Error('Erro ao gravar dados.');
    })
    .then(data => {
        mostrarMensagem('Sucesso ao gravar dados.');
        ler(false);
    })
    .catch(error => {
        console.error('Erro:', error);
        mostrarMensagem('Erro ao gravar dados.');
    });
}

function ler(mostrarMensagem = true) {
    function mostrarAlerta(mensagem) {
        if (mostrarMensagem) {
            alert(mensagem);
        }
    }
    
    fetch('ler.php')
    .then(response => {
        if (response.ok) {
            return response.clone().json();
        }
        throw new Error('Erro ao ler dados.');
    })
    .then(data => {
        if (!data || data.length === 0) {
            mostrarAlerta('Sem dados na tabela.'); // Exibe a mensagem apenas se os dados estiverem vazios
            return; // Sai da função sem executar o restante do código
        }
        pessoas = data;
        renderizarTabela();
        atualizarJsonOutput();
        if (mostrarMensagem) {
            mostrarAlerta('Dados lidos com sucesso.');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        if (mostrarMensagem) {
            mostrarAlerta('Erro ao ler dados.');
        }
    });
}





function removerFilho(id) {
    const indexPessoa = pessoas.findIndex(pessoa => pessoa.filhos.some(filho => filho.id === id));
    if (indexPessoa !== -1) {
        const indexFilho = pessoas[indexPessoa].filhos.findIndex(filho => filho.id === id);
        if (indexFilho !== -1) {
            pessoas[indexPessoa].filhos.splice(indexFilho, 1); // Remove o filho da lista de filhos na pessoa
            renderizarTabela();
            atualizarJsonOutput();
            return; // Sai da função se o filho foi encontrado e removido localmente
        }
    }

    fetch(`removerFilho.php?id=${id}`, {
        method: 'DELETE'
    })
    .then(response => {
        if (response.ok) {
            renderizarTabela();
            atualizarJsonOutput();
            ler(false); 
            return response.text();
        }
        throw new Error('Erro ao remover filho.');
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao remover filho.');
    });
}


function removerPessoa(index) {
    if (index >= 0 && index < pessoas.length) {
        const pessoa = pessoas[index];
        pessoas.splice(index, 1);

        fetch(`removerPessoa.php?id=${pessoa.id}`, {
            method: 'DELETE'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro ao remover pessoa.');
            }
            renderizarTabela();
            atualizarJsonOutput();
            return response.text();
        })
        .then(data => {
            console.log(data);
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao remover pessoa.');
        });
    }
}
