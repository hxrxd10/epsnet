/**
 * Reduce una foto en el navegador antes de subirla: la deja en JPEG con su lado mayor de hasta
 * `ladoMaximo` píxeles. Al redibujarla se descartan sus metadatos (como la ubicación del teléfono).
 * Si el navegador no puede procesarla, devuelve el archivo original.
 */
export async function reducirImagen(
    archivo: File,
    ladoMaximo = 1600,
    calidad = 0.85,
): Promise<File> {
    try {
        const imagen = await createImageBitmap(archivo, {
            imageOrientation: 'from-image',
        });
        const escala = Math.min(
            1,
            ladoMaximo / Math.max(imagen.width, imagen.height),
        );
        const lienzo = document.createElement('canvas');
        lienzo.width = Math.round(imagen.width * escala);
        lienzo.height = Math.round(imagen.height * escala);

        const contexto = lienzo.getContext('2d');

        if (contexto === null) {
            return archivo;
        }

        contexto.drawImage(imagen, 0, 0, lienzo.width, lienzo.height);
        imagen.close();

        const blob = await new Promise<Blob | null>((resolver) =>
            lienzo.toBlob(resolver, 'image/jpeg', calidad),
        );

        if (blob === null) {
            return archivo;
        }

        const nombre = archivo.name.replace(/\.[^.]+$/, '') || 'foto';

        return new File([blob], `${nombre}.jpg`, { type: 'image/jpeg' });
    } catch {
        return archivo;
    }
}
