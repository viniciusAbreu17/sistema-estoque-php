<?php
function validarProduto(array $post): array
{
    $produto = [
        'nome'         => trim($post['nome'] ?? ''),
        'descricao'    => trim($post['descricao'] ?? ''),
        'preco'        => str_replace(',', '.', trim($post['preco'] ?? '')),
        'quantidade'   => trim($post['quantidade'] ?? ''),
        'categoria_id' => $post['categoria_id'] ?? '',
    ];

    $erro = '';

    if ($produto['nome'] === '') {
        $erro = 'Informe o nome do produto.';
    } elseif (!is_numeric($produto['preco']) || $produto['preco'] < 0) {
        $erro = 'Preço inválido.';
    } elseif (filter_var($produto['quantidade'], FILTER_VALIDATE_INT) === false || $produto['quantidade'] < 0) {
        $erro = 'Quantidade inválida.';
    }

    return [$produto, $erro];
}