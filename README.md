# API para envio do censo de ocupação da Unimed Concordia (api_censo_concordia)

API desenvolvida em PHP, para realizar o envio dos dados para outra API.

---

## Tecnologias Utilizadas

- PHP 8.2.4

## Variaveis de Ambiente

- API_URL
- API_USER
- API_PASS
- DB_CONNECTION
- DB_HOST
- DB_PORT
- DB_SERVICE
- DB_DATABASE
- DB_USERNAME
- DB_PASSWORD
- DB_CHARSET


## Estrutura do Projeto
```
├── includes/           # Arquivos de apoio (conexões, funções globais)
├── src/                # Código fonte principal
├── oracle/             # Componentes necessários a construção do container
├── LICENSE.txt         # Licença do projeto
├── .gitignore          # Arquivos ignorados pelo Git
```

### Instalar dependências
composer install

### Copiar arquivo de variáveis de ambiente
cp .env.example

### Editar o arquivo .env com suas configurações locais

### Executar o servidor local (exemplo)
php -S localhost:8000 -t public/

## Endpoints para envio dos dados
├── $base_url/auth/login    
Endpoint utilizado para autenticação antes do envio. Método POST

├── $base_url/enviar        
Endpoint utilizado para envio dos dados do censo. Método POST
