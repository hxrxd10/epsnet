import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Spinner } from '@/components/ui/spinner';

type Props = {
    titulo: string;
    descripcion: string;
    abierto: boolean;
    procesando: boolean;
    onCancelar: () => void;
    onConfirmar: () => void;
};

export default function DialogoEliminar({
    titulo,
    descripcion,
    abierto,
    procesando,
    onCancelar,
    onConfirmar,
}: Props) {
    return (
        <Dialog open={abierto} onOpenChange={(valor) => !valor && onCancelar()}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{titulo}</DialogTitle>
                    <DialogDescription>{descripcion}</DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" onClick={onCancelar}>
                        Cancelar
                    </Button>
                    <Button
                        variant="destructive"
                        disabled={procesando}
                        onClick={onConfirmar}
                    >
                        {procesando && <Spinner />}
                        Eliminar
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
