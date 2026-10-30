/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./public/**/*.php",
    "./public/**/*.js",
    "./app/Views/**/*.php",
  ],
  theme: {
    extend: {
      colors: {
        elcar: {
          blue: '#123B5D',      // Azul principal (navegação, cabeçalho, títulos)
          secondary: '#1F5F91', // Azul secundário (links, destaques, ativos)
          yellow: '#F2C230',    // Amarelo de destaque (CTAs, ícones, detalhes)
          bg: '#F4F7FA',        // Fundo cinza muito claro
          text: '#1C2933',      // Texto grafite escuro
        }
      }
    },
  },
  plugins: [],
}