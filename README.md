## Balance API

API em Laravel para manipular saldos de conta utilizando um storage em arquivo JSON, sem dependências de banco de dados.

### Arquitetura
- **Controllers** recebem as requisições HTTP.
- **Services** concentram a regra de negócio para cada caso de uso.
- **Utils** cuidam da persistência em `storage/app/balance-data.json`.

Esse formato segue um padrão em camadas (Controller ➜ Service ➜ Util) que desacopla HTTP da lógica e da persistência em arquivo.

### Rotas
- `GET /balance?account_id={id}`
  Retorna `200` e o saldo da conta (`number`) ou `404` com `0` quando a conta não existe.
- `POST /event`
  Processa eventos financeiros. Payload esperado:
  ```json
  // depósito
  { "type": "deposit", "destination": "100", "amount": 20 }

  // saque
  { "type": "withdraw", "origin": "100", "amount": 15 }

  // transferência
  { "type": "transfer", "origin": "100", "destination": "300", "amount": 10 }
  ```
  Os saldos são persistidos/criados automaticamente no JSON.
- `POST /reset`
  Limpa o estado do arquivo de saldos e retorna `OK`.

### Como executar
1. Instale as dependências:
   ```bash
   composer install
   ```
2. Copie o arquivo de ambiente (se ainda não existir) e gere a chave:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
3. Suba o servidor de desenvolvimento:
   ```bash
   php artisan serve
   ```
4. Para testes externos, exponha a porta com o Ngrok após o servidor estar de pé:
   ```bash
   ngrok http 8000
   ```
   Utilize a URL pública fornecida pelo Ngrok para chamar as rotas acima.

### Persistência
Os saldos ficam em `storage/app/balance-data.json`, estruturados como:
```json
{
  "100": { "balance": 20 },
  "300": { "balance": 5 }
}
```
Esse arquivo é recriado/atualizado automaticamente pelos métodos da classe `App\Http\Utils\Util`.
