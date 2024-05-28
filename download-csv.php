<?php
// Configurações de acesso FTP
$ftp_server = '223.27.112.180';
$ftp_username = 'u760716220.m3social.dev.br';
$ftp_password = '2K7Uj4ztiJ';

// Caminho onde os arquivos serão baixados localmente
$local_directory = '/Users/josefsantos/Downloads/Resultados/';

// Conectar-se ao servidor FTP
$conn_id = ftp_connect($ftp_server);
$login_result = ftp_login($conn_id, $ftp_username, $ftp_password);

// Verificar a conexão
if ((!$conn_id) || (!$login_result)) {
    echo 'Falha ao conectar-se ao servidor FTP!';
    exit;
} else {
    echo 'Conexão estabelecida com sucesso ao servidor FTP!';

    // Configuração do tempo limite da conexão FTP
    ftp_set_option($conn_id, FTP_TIMEOUT_SEC, 300); // Define o tempo limite para 300 segundos (5 minutos)

    $numero_resultado = 1;
    $arquivo_encontrado = false;

    // Loop para procurar por arquivos sequenciais
    while (!$arquivo_encontrado) {
        // Nome do arquivo CSV no servidor FTP
        $remote_file = '/public_html/dreamcar/resultados/Resultados' . $numero_resultado . '.csv';
        // Caminho completo do arquivo local com o mesmo nome
        $local_file = $local_directory . 'Resultados' . $numero_resultado . '.csv';

        // Verifica se o arquivo existe localmente
        if (!file_exists($local_file)) {
            // Verifica se o arquivo existe no servidor FTP
            if (ftp_size($conn_id, $remote_file) != -1) {
                // Tentar baixar o arquivo
                if (ftp_get($conn_id, $local_file, $remote_file, FTP_BINARY)) {
                    echo "Arquivo CSV '$remote_file' baixado com sucesso!\n";
                    $numero_resultado++; // Incrementa para verificar o próximo arquivo sequencial
                } else {
                    echo "Erro ao baixar o arquivo CSV '$remote_file'!\n";
                    break; // Sai do loop em caso de erro
                }
            } else {
                echo "Arquivo CSV '$remote_file' não encontrado. Finalizando...\n";
                break; // Sai do loop se o arquivo não for encontrado
            }
        } else {
            echo "Arquivo CSV '$local_file' já existe localmente. Pulando para o próximo arquivo...\n";
            $numero_resultado++; // Incrementa para verificar o próximo arquivo sequencial
        }
    }

    // Fechar a conexão FTP
    ftp_close($conn_id);
}
?>