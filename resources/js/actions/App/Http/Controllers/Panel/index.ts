import BandejaController from './BandejaController'
import EstudianteController from './EstudianteController'
import OrdenImpresionController from './OrdenImpresionController'
import VerificacionExpedienteController from './VerificacionExpedienteController'

const Panel = {
    BandejaController: Object.assign(BandejaController, BandejaController),
    EstudianteController: Object.assign(EstudianteController, EstudianteController),
    OrdenImpresionController: Object.assign(OrdenImpresionController, OrdenImpresionController),
    VerificacionExpedienteController: Object.assign(VerificacionExpedienteController, VerificacionExpedienteController),
}

export default Panel