<!doctype html>
<html lang="en" class="scroll-smooth">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>TaskForge - @yield('title')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              brand: {
                50: "#fff7ed",
                100: "#ffedd5",
                200: "#fed7aa",
                300: "#fdba74",
                400: "#fb923c",
                500: "#f97316",
                600: "#ea580c",
                700: "#c2410c",
                800: "#9a3412",
                900: "#7c2d12",
              },
            },
            boxShadow: {
              soft: "0 10px 30px rgba(15, 23, 42, 0.08)",
            },
          },
        },
      };
    </script>
  </head>
  <body class="bg-white text-slate-900 antialiased">
    @yield('content')
  </body>
</html>
