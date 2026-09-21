import Auth from './Auth'
import EstadisticaController from './EstadisticaController'
import RepositorioController from './RepositorioController'
import Panel from './Panel'
import Admin from './Admin'
import Settings from './Settings'
import Estudiante from './Estudiante'

const Controllers = {
    Auth: Object.assign(Auth, Auth),
    EstadisticaController: Object.assign(EstadisticaController, EstadisticaController),
    RepositorioController: Object.assign(RepositorioController, RepositorioController),
    Panel: Object.assign(Panel, Panel),
    Admin: Object.assign(Admin, Admin),
    Settings: Object.assign(Settings, Settings),
    Estudiante: Object.assign(Estudiante, Estudiante),
}

export default Controllers