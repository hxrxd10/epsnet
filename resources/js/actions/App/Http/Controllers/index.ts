import Auth from './Auth'
import EstadisticaController from './EstadisticaController'
import EstadisticaPdfController from './EstadisticaPdfController'
import OdsController from './OdsController'
import RepositorioController from './RepositorioController'
import InvestigacionController from './InvestigacionController'
import FotoExpedienteController from './FotoExpedienteController'
import Panel from './Panel'
import Admin from './Admin'
import Settings from './Settings'
import Estudiante from './Estudiante'

const Controllers = {
    Auth: Object.assign(Auth, Auth),
    EstadisticaController: Object.assign(EstadisticaController, EstadisticaController),
    EstadisticaPdfController: Object.assign(EstadisticaPdfController, EstadisticaPdfController),
    OdsController: Object.assign(OdsController, OdsController),
    RepositorioController: Object.assign(RepositorioController, RepositorioController),
    InvestigacionController: Object.assign(InvestigacionController, InvestigacionController),
    FotoExpedienteController: Object.assign(FotoExpedienteController, FotoExpedienteController),
    Panel: Object.assign(Panel, Panel),
    Admin: Object.assign(Admin, Admin),
    Settings: Object.assign(Settings, Settings),
    Estudiante: Object.assign(Estudiante, Estudiante),
}

export default Controllers