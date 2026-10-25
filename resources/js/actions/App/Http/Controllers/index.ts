import Auth from './Auth'
import FotoExpedienteController from './FotoExpedienteController'
import EstadisticaController from './EstadisticaController'
import EstadisticaPdfController from './EstadisticaPdfController'
import EstadisticaExcelController from './EstadisticaExcelController'
import OdsController from './OdsController'
import RepositorioController from './RepositorioController'
import InvestigacionController from './InvestigacionController'
import Panel from './Panel'
import Admin from './Admin'
import Settings from './Settings'
import Estudiante from './Estudiante'

const Controllers = {
    Auth: Object.assign(Auth, Auth),
    FotoExpedienteController: Object.assign(FotoExpedienteController, FotoExpedienteController),
    EstadisticaController: Object.assign(EstadisticaController, EstadisticaController),
    EstadisticaPdfController: Object.assign(EstadisticaPdfController, EstadisticaPdfController),
    EstadisticaExcelController: Object.assign(EstadisticaExcelController, EstadisticaExcelController),
    OdsController: Object.assign(OdsController, OdsController),
    RepositorioController: Object.assign(RepositorioController, RepositorioController),
    InvestigacionController: Object.assign(InvestigacionController, InvestigacionController),
    Panel: Object.assign(Panel, Panel),
    Admin: Object.assign(Admin, Admin),
    Settings: Object.assign(Settings, Settings),
    Estudiante: Object.assign(Estudiante, Estudiante),
}

export default Controllers