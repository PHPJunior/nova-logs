import Dashboard from './pages/Dashboard';
import LogsTool from './pages/LogsTool';
import ShowLogs from './pages/ShowLogs';

Nova.booting((app, store) => {
    Nova.inertia('NovaLogs', Dashboard);
    Nova.inertia('LogsTool', LogsTool);
    Nova.inertia('ShowLogs', ShowLogs);
})
