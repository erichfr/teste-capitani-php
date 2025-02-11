<?php
$demandas = $demandas ?? [];
$tipo = $_GET['TIPO'] ?? 1;
$info = $_GET['INFO'] ?? '';
$successMessage = $_SESSION['successMessage'] ?? null;
$errorMessage = $_SESSION['errorMessage'] ?? null;

unset($_SESSION['successMessage'], $_SESSION['errorMessage']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Consulta de Demandas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h1 class="text-center mb-4">Consultar Demandas</h1>

        <div class="text-end mt-3 mb-4">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAdicionarDemanda">
                <i class="bi bi-plus-lg"></i> Adicionar Demanda
            </button>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form action="/consulta-demanda" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="tipo" class="form-label">Tipo:</label>
                        <select name="TIPO" id="tipo" class="form-select">
                            <option value="1" <?= ($tipo == 1) ? 'selected' : '' ?>>Código</option>
                            <option value="2" <?= ($tipo == 2) ? 'selected' : '' ?>>Descrição</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="info" class="form-label">Informação:</label>
                        <input type="text" name="INFO" id="info" value="<?= htmlspecialchars($info) ?>" class="form-control" placeholder="Digite o código ou descrição">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Buscar</button>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($successMessage): ?>
            <div class="alert alert-success mt-3"><?= $successMessage ?></div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <div class="alert alert-danger mt-3"><?= $errorMessage ?></div>
        <?php endif; ?>

        <div class="card mt-4 shadow-sm">
            <div class="card-body">
                <h2 class="card-title mb-3">Resultados</h2>
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Código</th>
                            <th>Descrição</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($demandas) && is_array($demandas)) : ?>
                            <?php foreach ($demandas as $demanda) : ?>
                                <!-- Verifique se $demanda é um array e tem os índices esperados antes de acessá-los -->
                                <?php if (is_array($demanda)) : ?>
                                    <tr>
                                        <td><?= htmlspecialchars($demanda['codigo']) ?></td>
                                        <td><?= htmlspecialchars($demanda['descricao']) ?></td>
                                        <td>
                                            <button class="btn btn-info btn-sm" onclick="verDetalhes(<?= htmlspecialchars(json_encode($demanda)) ?>)">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="btn btn-warning btn-sm" onclick="editarDemanda(<?= htmlspecialchars(json_encode($demanda)) ?>)">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm" onclick="confirmarExclusao(<?= htmlspecialchars(json_encode($demanda)) ?>)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted">
                                    <?= $_SESSION['errorMessage'] ?? "Nenhuma demanda encontrada." ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>

<div class="modal fade" id="modalAdicionarDemanda" tabindex="-1" aria-labelledby="modalAdicionarDemandaLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAdicionarDemandaLabel">Adicionar Nova Demanda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <form action="/consulta-demanda" method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="descricao" class="form-label">Descrição</label>
                            <input type="text" name="descricao" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="descriweb" class="form-label">Descrição Web</label>
                            <input type="text" name="descriweb" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="tipo" class="form-label">Tipo</label>
                            <input type="text" name="tipo" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="grupo" class="form-label">Grupo</label>
                            <input type="text" name="grupo" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="area" class="form-label">Área</label>
                            <input type="text" name="area" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="ativo" class="form-label">Ativo</label>
                            <input type="text" name="ativo" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="atendimento" class="form-label">Atendimento</label>
                            <input type="text" name="atendimento" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="prazo" class="form-label">Prazo</label>
                            <input type="number" name="prazo" class="form-control" required>
                        </div>
                    </div>
                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-success">Salvar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetalhes" tabindex="-1" aria-labelledby="modalDetalhesLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetalhesLabel">Detalhes da Demanda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <pre id="jsonDetalhes"></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditarDemanda" tabindex="-1" aria-labelledby="modalEditarDemandaLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarDemandaLabel">Editar Demanda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <form id="formEditarDemanda">
                    <input type="hidden" id="editCodigo" name="codigo">
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" id="csrfToken" value="<?= $_SESSION['_token'] ?? '' ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="editDescricao" class="form-label">Descrição</label>
                            <input type="text" name="descricao" id="editDescricao" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="editDescriweb" class="form-label">Descrição Web</label>
                            <input type="text" name="descriweb" id="editDescriweb" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="editTipo" class="form-label">Tipo</label>
                            <input type="text" name="tipo" id="editTipo" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="editGrupo" class="form-label">Grupo</label>
                            <input type="text" name="grupo" id="editGrupo" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="editArea" class="form-label">Área</label>
                            <input type="text" name="area" id="editArea" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="editAtivo" class="form-label">Ativo</label>
                            <input type="text" name="ativo" id="editAtivo" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="editAtendimento" class="form-label">Atendimento</label>
                            <input type="text" name="atendimento" id="editAtendimento" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="editPrazo" class="form-label">Prazo</label>
                            <input type="number" name="prazo" id="editPrazo" class="form-control" required>
                        </div>
                    </div>
                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-success">Salvar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalConfirmarExclusao" tabindex="-1" aria-labelledby="modalConfirmarExclusaoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmarExclusaoLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza de que deseja excluir esta demanda?</p>
                <p><strong>Código:</strong> <span id="codigoExcluir"></span></p>
                <p><strong>Descrição:</strong> <span id="descricaoExcluir"></span></p>
            </div>
            <div class="modal-footer">
                <button id="btnConfirmarExclusao" class="btn btn-danger">Sim, excluir</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>

        function verDetalhes(demanda) {
            document.getElementById("jsonDetalhes").textContent = JSON.stringify(demanda, null, 4);
            var modal = new bootstrap.Modal(document.getElementById("modalDetalhes"));
            modal.show();
        }   

        function editarDemanda(demanda) {
            console.log("Demanda recebida:", demanda); // 🔍 Depuração

            document.getElementById("editCodigo").value = demanda.codigo;
            document.getElementById("editDescricao").value = demanda.descricao;
            document.getElementById("editDescriweb").value = demanda.descricaoweb;

            document.getElementById("editTipo").value = demanda.tipo?.codigo ?? "";
            document.getElementById("editGrupo").value = demanda.grupo?.codigo ?? "";
            document.getElementById("editArea").value = demanda.area?.codigo ?? "";
            document.getElementById("editAtivo").value = demanda.ativo?.codigo ?? "";
            document.getElementById("editAtendimento").value = demanda.atendimento?.codigo ?? "";
            document.getElementById("editPrazo").value = 10 ?? 0;

            var modal = new bootstrap.Modal(document.getElementById("modalEditarDemanda"));
            modal.show();
        }

        document.getElementById("formEditarDemanda").addEventListener("submit", function (event) {
            event.preventDefault();

            let codigo = document.getElementById("editCodigo").value; 
            let formData = {
                descricao: document.getElementById("editDescricao").value,
                descriweb: document.getElementById("editDescriweb").value,
                tipo: document.getElementById("editTipo").value,
                grupo: document.getElementById("editGrupo").value,
                area: document.getElementById("editArea").value,
                ativo: document.getElementById("editAtivo").value,
                atendimento: document.getElementById("editAtendimento").value,
                prazo: parseInt(document.getElementById("editPrazo").value),
            };

            let csrfToken = document.getElementById("csrfToken").value; 

            fetch(`/consulta-demanda/${codigo}`, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken 
                },
                body: JSON.stringify(formData),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert("Erro ao atualizar a demanda: " + (data.error || "Erro desconhecido."));
                }
            })
            .catch(error => {
                console.error("Erro ao atualizar demanda:", error);
                alert("Erro ao atualizar a demanda.");
            });
        });

        let codigoParaExcluir = null; 

        function confirmarExclusao(demanda) {
            document.getElementById("codigoExcluir").textContent = demanda.codigo;
            document.getElementById("descricaoExcluir").textContent = demanda.descricao;

            codigoParaExcluir = demanda.codigo;

            var modal = new bootstrap.Modal(document.getElementById("modalConfirmarExclusao"));
            modal.show();
        }

        document.getElementById("btnConfirmarExclusao").addEventListener("click", function () {
            if (!codigoParaExcluir) {
                alert("Erro: Código da demanda não encontrado.");
                return;
            }

            fetch("/consulta-demanda", {
                method: "DELETE",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ codigo: codigoParaExcluir }), 
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Demanda excluída com sucesso!");

                    window.location.href = window.location.pathname + window.location.search;
                } else {
                    alert("Erro ao excluir a demanda: " + (data.error || "Erro desconhecido."));
                }
            })
            .catch(error => {
                console.error("Erro ao excluir demanda:", error);
                alert("Erro ao excluir a demanda.");
            });
        });

    </script>

</body>
</html>
