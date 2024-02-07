let pessoas = [];

// Função para adicionar uma pessoa à lista de pessoas
function adicionarPessoa() {
    const nome = document.getElementById("nomeInput").value;
    if (nome) {
        const id = pessoas.length + 1; // Gere um ID único para a pessoa
        pessoas.push({ id: id, nome: nome, filhos: [] });

        // Adicione o console.log(pessoas) para verificar os IDs gerados
        console.log(pessoas);

        renderizarTabela();
        atualizarJsonOutput();
    }
}



// Função para renderizar a tabela de pessoas e filhos
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



// Função para adicionar um filho a uma pessoa
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

// Função para confirmar a adição de um filho
function confirmarFilho(index) {
    const nomeFilho = document.getElementById("nomeFilhoInput").value;
    if (nomeFilho) {
        const idFilho = pessoas[index].filhos.length + 1; // Gere um ID único para o filho
        pessoas[index].filhos.push({ id: idFilho, nome: nomeFilho });
        renderizarTabela();
        atualizarJsonOutput();
    }
    const dialog = document.querySelector("dialog");
    dialog.close();
    dialog.remove();
}
// Função para atualizar o conteúdo do textarea com o JSON atualizado
function atualizarJsonOutput() {
    document.getElementById("jsonOutput").value = JSON.stringify(pessoas, null, 2);
}



function gravar() {
    const json = JSON.stringify(pessoas);
    function mostrarMensagem(mensagem) {
        alert(mensagem);
    }
    
    fetch('gravarDados.php', {
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
        console.log(data);
        mostrarMensagem('Sucesso ao gravar dados.');
        ler(); // Chama a função ler() após a gravação bem-sucedida
    })
    .catch(error => {
        console.error('Erro:', error);
        mostrarMensagem('Erro ao gravar dados.');
    });
}




function ler() {
    function mostrarMensagem(mensagem) {
        alert(mensagem);
    }
    
    fetch('ler.php')
    .then(response => {
        if (response.ok) {
            return response.clone().json();
        }
        throw new Error('Erro ao ler dados.');
    })
    .then(data => {
        if (!data || Object.keys(data).length === 0) {
            throw new Error('Resposta vazia ou malformada.');
        }
        pessoas = data;
        renderizarTabela();
        atualizarJsonOutput();
        mostrarMensagem('Dados lidos com sucesso.');
    })
    .catch(error => {
        console.error('Erro:', error);
        mostrarMensagem('Erro ao ler dados.');
    });
}
// Função para remover um filho pelo ID
function removerFilho(id) {
    const pessoaIndex = pessoas.findIndex(pessoa => pessoa.filhos.some(filho => filho.id === id));
    if (pessoaIndex !== -1) {
        const filhoIndex = pessoas[pessoaIndex].filhos.findIndex(filho => filho.id === id);
        if (filhoIndex !== -1) {
            pessoas[pessoaIndex].filhos.splice(filhoIndex, 1); // Remove o filho do array de filhos da pessoa

            // Enviar solicitação DELETE para o servidor para remover o filho
            fetch(`removerFilho.php?id=${id}`, {
                method: 'DELETE'
            })
            .then(response => {
                if (response.ok) {
                    renderizarTabela();
                    atualizarJsonOutput();
                    return response.text();
                }
                throw new Error('Erro ao remover filho.');
            })
            .then(data => {
                console.log(data); // Resposta do servidor
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao remover filho.');
            });
        }
    }
}

// Função para remover uma pessoa pelo índice
function removerPessoa(index) {
    if (index >= 0 && index < pessoas.length) {
        const pessoa = pessoas[index];
        pessoas.splice(index, 1); // Remove a pessoa da lista de pessoas

        // Enviar solicitação DELETE para o servidor para remover a pessoa
        fetch(`removerPessoa.php?id=${pessoa.id}`, {
            method: 'DELETE'
        })
        .then(response => {
            if (response.ok) {
                renderizarTabela();
                atualizarJsonOutput();
                return response.text();
            }
            throw new Error('Erro ao remover pessoa.');
        })
        .then(data => {
            console.log(data); // Resposta do servidor
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao remover pessoa.');
        });
    }
}


