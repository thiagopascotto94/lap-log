# Grupo CRIAR - Desafio de Logica

## Lógica de Resolução do Desafio

### Passo 1 - Preparando dados

- **Extração dos dados** - Utilizado uma ferramenta propria para extrair a tabela. (https://thiagopascotto94.github.io/ocr-gemini/)
- **Conversão em JSON** - Para simular um banco de dados, converti os dados para JSON e salvos em `db/laps.json`

### Passo 2 - Exibição de Placar

- Criar função para converter tudo em segundos/milisegundos.
- Lógica simples, basta filtrar os corredores que completaram as 4 voltas, corredores que não completaram as 4 voltas serão considerados desclassificados;
- Para obter o pódio, basta filtrar todas as 4 voltas ordenados pela hora de forma decrescente, ou seja, o corredor com data/hora mais antiga é o primeiro colocado e o restante na sequencia.

### Passo 3 - Obtenção dos dados exclusivos de cada piloto

- Aqui, a lógica será separar todos os pilotos/corredores disponiveis na corrida;
- Depois, filtrar e agrupar os dados exclusivos de cada corredor;

### Passo 4 - Obtendo a melhor volta de cada piloto

- Com uma nova ordenação nos dados exclusivos de cada corredos em "Tempo Volta" de forma crescente, onde o primeiro resultado indicará a volta mais curta (consequentemente a mais rapida) ou "Velocidade média da volta" de forma decrescente, onde o primeiro indica tambem que a volta foi feita com mais velocidade, consequentemente mais rapida.

### Passo 5 - Obtendo a média de velocidade na corrida de cada piloto

- Com os dados já separados no "Passo 3", basta somar todas as velocidades médias de cada volta, e dividir pela quantidade total de voltas.

### Passo 6 - Tempo de chegada do segundo colocado em diante

- Obter os dados do primeiro colocado;
- Criar uma listagem sem o primeiro colocado para os calculos;
- Filtrar os corredores/pilotos que completaram as 4 voltas;
- Iterar os dados comparando com os dados de "Hora" dos corredores;

### Como gero meus commits?

- Utilizo uma combinação de `git diff` e IA para gerar no formato que eu gosto;
- Para acelerar minha produtividade eu criei um agente especialista em gerar a mensagens de commit no formato que eu acredito ser o melhor.

### Layout 
- Feito com uso de IA.
