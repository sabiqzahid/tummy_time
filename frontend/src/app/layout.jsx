import "@/styles/globals.css";

export const metadata = {
  title: "Tummy Time",
  description: "Tummy Time Restaurant App makes your tummy happy!",
};

export default function RootLayout({ children }) {
  return (
    <html lang="en">
      <body>{children}</body>
    </html>
  );
}
