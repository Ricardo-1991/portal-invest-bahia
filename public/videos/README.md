# Vídeo do hero

Coloque aqui o arquivo `hero.mp4` para que o hero da home passe a rodar vídeo
em vez da imagem estática.

- **Nome exato:** `hero.mp4`
- **Formato:** MP4 (H.264 + AAC, ou sem faixa de áudio — o vídeo roda mudo)
- **Proporção sugerida:** 16:9, largura mínima de 1920px
- **Duração:** 10 a 25 segundos, com corte que fecha em loop

Enquanto o arquivo não existir, o navegador exibe a `images/hero-rural-bahia.png`
como poster e o hero fica idêntico ao de antes — sem erro no console. Assim que
o MP4 for colocado aqui, o vídeo passa a rodar sozinho, sem alterar código.

O vídeo nunca toca quando o visitante tem `prefers-reduced-motion: reduce`
ativado no sistema, e pausa automaticamente quando o hero sai da tela.

## Peso

Comprima antes de commitar. Referência com ffmpeg:

```bash
ffmpeg -i original.mov -vcodec libx264 -crf 30 -preset slow -an \
  -vf "scale=1920:-2" -movflags +faststart hero.mp4
```

Acima de ~8 MB, vale tratar como asset de deploy em vez de versionar no git.
