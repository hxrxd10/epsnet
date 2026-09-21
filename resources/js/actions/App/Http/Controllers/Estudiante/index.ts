import AccesoController from './AccesoController'
import CuentaController from './CuentaController'
import ExpedienteController from './ExpedienteController'
import BienServicioController from './BienServicioController'
import PublicacionInvestigacionController from './PublicacionInvestigacionController'
import TransferenciaConocimientoController from './TransferenciaConocimientoController'
import UbicacionTerritorialController from './UbicacionTerritorialController'
import ActorParticipanteController from './ActorParticipanteController'
import SeguimientoImpactoController from './SeguimientoImpactoController'
import CierreExpedienteController from './CierreExpedienteController'
import OrdenImpresionController from './OrdenImpresionController'

const Estudiante = {
    AccesoController: Object.assign(AccesoController, AccesoController),
    CuentaController: Object.assign(CuentaController, CuentaController),
    ExpedienteController: Object.assign(ExpedienteController, ExpedienteController),
    BienServicioController: Object.assign(BienServicioController, BienServicioController),
    PublicacionInvestigacionController: Object.assign(PublicacionInvestigacionController, PublicacionInvestigacionController),
    TransferenciaConocimientoController: Object.assign(TransferenciaConocimientoController, TransferenciaConocimientoController),
    UbicacionTerritorialController: Object.assign(UbicacionTerritorialController, UbicacionTerritorialController),
    ActorParticipanteController: Object.assign(ActorParticipanteController, ActorParticipanteController),
    SeguimientoImpactoController: Object.assign(SeguimientoImpactoController, SeguimientoImpactoController),
    CierreExpedienteController: Object.assign(CierreExpedienteController, CierreExpedienteController),
    OrdenImpresionController: Object.assign(OrdenImpresionController, OrdenImpresionController),
}

export default Estudiante