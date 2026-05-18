import { useState, useEffect } from 'react'
import {
  Typography,
  Paper,
  TextField,
  Box,
  CircularProgress,
  Avatar
} from '@mui/material'
import PersonIcon from '@mui/icons-material/Person'
import api from '../api/axios'

export default function Profile() {
  const [user, setUser] = useState(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    api.get('/auth/me')
      .then((res) => setUser(res.data.data))
      .catch(() => {})
      .finally(() => setLoading(false))
  }, [])

  if (loading) {
    return (
      <Box sx={{ display: 'flex', justifyContent: 'center', mt: 10 }}>
        <CircularProgress />
      </Box>
    )
  }

  return (
    <div>
      <Typography variant="h4" gutterBottom>
        Mi Perfil
      </Typography>

      <Paper sx={{ p: 3, maxWidth: 600 }}>
        <Box sx={{ display: 'flex', alignItems: 'center', gap: 2, mb: 3 }}>
          <Avatar sx={{ width: 64, height: 64, bgcolor: 'primary.main' }}>
            <PersonIcon sx={{ fontSize: 36 }} />
          </Avatar>
          <div>
            <Typography variant="h6">
              {user?.name} {user?.last_name}
            </Typography>
            <Typography variant="body2" color="text.secondary">
              {user?.role === 'admin' ? 'Administrador' : 'Usuario'}
            </Typography>
          </div>
        </Box>

        <TextField
          fullWidth
          label="Nombre"
          value={user?.name || ''}
          InputProps={{ readOnly: true }}
          sx={{ mb: 2 }}
        />

        <TextField
          fullWidth
          label="Apellido"
          value={user?.last_name || ''}
          InputProps={{ readOnly: true }}
          sx={{ mb: 2 }}
        />

        <TextField
          fullWidth
          label="Correo electrónico"
          value={user?.email || ''}
          InputProps={{ readOnly: true }}
          sx={{ mb: 2 }}
        />

        <TextField
          fullWidth
          label="Teléfono"
          value={user?.phone || ''}
          InputProps={{ readOnly: true }}
          sx={{ mb: 2 }}
        />

        <TextField
          fullWidth
          label="Rol"
          value={user?.role === 'admin' ? 'Administrador' : 'Usuario'}
          InputProps={{ readOnly: true }}
          sx={{ mb: 2 }}
        />

        <TextField
          fullWidth
          label="Estado"
          value={user?.status === 1 ? 'Activo' : 'Inactivo'}
          InputProps={{ readOnly: true }}
        />
      </Paper>
    </div>
  )
}
