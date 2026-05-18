import { useNavigate, useLocation } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'
import {
  Drawer,
  List,
  ListItem,
  ListItemButton,
  ListItemIcon,
  ListItemText,
  Divider,
  Box,
  Typography,
  Toolbar
} from '@mui/material'
import DashboardIcon from '@mui/icons-material/Dashboard'
import ReceiptIcon from '@mui/icons-material/Receipt'
import DescriptionIcon from '@mui/icons-material/Description'
import PersonIcon from '@mui/icons-material/Person'
import LogoutIcon from '@mui/icons-material/Logout'

const drawerWidth = 240

const menuItems = [
  { text: 'Dashboard', icon: <DashboardIcon />, path: '/dashboard' },
  { text: 'Facturas', icon: <ReceiptIcon />, path: '/invoices' },
  { text: 'Informe', icon: <DescriptionIcon />, path: '/report' },
  { text: 'Mi Perfil', icon: <PersonIcon />, path: '/profile' }
]

export default function Sidebar({ children }) {
  const { user, logout } = useAuth()
  const navigate = useNavigate()
  const location = useLocation()

  const handleLogout = () => {
    logout()
    navigate('/login')
  }

  return (
    <Box sx={{ display: 'flex' }}>
      <Drawer
        variant="permanent"
        sx={{
          width: drawerWidth,
          '& .MuiDrawer-paper': {
            width: drawerWidth
          }
        }}
      >
        <Toolbar sx={{ justifyContent: 'center', py: 2 }}>
          <img src="/images/logo.png" alt="Logo" style={{ maxWidth: 200, height: 'auto' }} />
        </Toolbar>

        <Divider />

        <List>
          {menuItems.map((item) => (
            <ListItem key={item.text} disablePadding>
              <ListItemButton
                selected={location.pathname === item.path}
                onClick={() => navigate(item.path)}
              >
                <ListItemIcon>{item.icon}</ListItemIcon>
                <ListItemText primary={item.text} />
              </ListItemButton>
            </ListItem>
          ))}
        </List>

        <Divider />

        <List>
          <ListItem disablePadding>
            <ListItemButton onClick={handleLogout}>
              <ListItemIcon><LogoutIcon /></ListItemIcon>
              <ListItemText primary="Cerrar Sesión" />
            </ListItemButton>
          </ListItem>
        </List>

        <Box sx={{ p: 2, mt: 'auto' }}>
          <Typography variant="body2" noWrap>
            {user?.name} {user?.last_name}
          </Typography>
          <Typography variant="caption" color="text.secondary" noWrap>
            {user?.email}
          </Typography>
        </Box>
      </Drawer>

      <Box
        component="main"
        sx={{
          flexGrow: 1,
          p: 3,
          width: `calc(100% - ${drawerWidth}px)`,
          minHeight: '100vh',
          backgroundColor: '#f5f5f5'
        }}
      >
        {children}
      </Box>
    </Box>
  )
}
