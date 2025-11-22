/**
 * Login Screen
 *
 * Authentication screen for users to login with email and password
 * - Form validation
 * - Loading states
 * - Error handling
 * - Navigation to register
 */

import React, { useState } from 'react';
import {
  View,
  StyleSheet,
  KeyboardAvoidingView,
  Platform,
  TouchableOpacity,
  Alert,
  TextInput,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { Lucide } from '@react-native-vector-icons/lucide';
import { useLogin } from '../../hooks/useAuth';
import Button from '../../components/atoms/Button';
import Text from '../../components/atoms/Text';
import { COLORS, SPACING, FONT_SIZES } from '../../config/constants';

type LoginScreenProps = NativeStackScreenProps<any, 'Login'>;

interface FormData {
  email: string;
  password: string;
}

export const LoginScreen: React.FC<LoginScreenProps> = ({ navigation }) => {
  const [formData, setFormData] = useState<FormData>({
    email: '',
    password: '',
  });
  const [errors, setErrors] = useState<Partial<FormData>>({});
  const [showPassword, setShowPassword] = useState(false);

  const loginMutation = useLogin();

  const validateForm = (): boolean => {
    const newErrors: Partial<FormData> = {};

    if (!formData.email) {
      newErrors.email = 'Email is required';
    } else if (!formData.email.includes('@')) {
      newErrors.email = 'Invalid email format';
    }

    if (!formData.password) {
      newErrors.password = 'Password is required';
    } else if (formData.password.length < 6) {
      newErrors.password = 'Password must be at least 6 characters';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleLogin = async () => {
    if (!validateForm()) {
      return;
    }

    try {
      await loginMutation.mutateAsync({
        email: formData.email,
        password: formData.password,
      });

      // Auth state automatically updates via hook
      // Navigation happens automatically via RootNavigator
    } catch (error: any) {
      Alert.alert(
        'Login Failed',
        error?.response?.data?.message || 'Invalid email or password',
        [{ text: 'OK' }],
      );
    }
  };

  return (
    <SafeAreaView style={styles.safeArea}>
      <KeyboardAvoidingView
        behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
        style={styles.container}
      >
        <View style={styles.content}>
          {/* Header */}
          <View style={styles.header}>
            <Text style={styles.title}>💕 Welcome Back</Text>
            <Text style={styles.subtitle}>Login to find your match</Text>
          </View>

          {/* Form */}
          <View style={styles.form}>
            {/* Email Input */}
            <View style={styles.inputContainer}>
              <Text style={styles.label}>Email</Text>
              <TextInput
                style={[styles.input, errors.email && styles.inputError]}
                placeholder="Enter your email"
                placeholderTextColor={COLORS.placeholder}
                value={formData.email}
                onChangeText={text => {
                  setFormData({ ...formData, email: text });
                  if (errors.email) {
                    setErrors({ ...errors, email: undefined });
                  }
                }}
                editable={!loginMutation.isPending}
              />
              {errors.email && (
                <Text style={[styles.errorText, { color: COLORS.danger }]}>
                  {errors.email}
                </Text>
              )}
            </View>

            {/* Password Input */}
            <View style={styles.inputContainer}>
              <Text style={styles.label}>Password</Text>
              <View style={styles.passwordInputWrapper}>
                <TextInput
                  style={[
                    styles.input,
                    styles.passwordInput,
                    errors.password && styles.inputError,
                  ]}
                  placeholder="Enter password"
                  placeholderTextColor={COLORS.placeholder}
                  value={formData.password}
                  onChangeText={text => {
                    setFormData({ ...formData, password: text });
                    if (errors.password) {
                      setErrors({ ...errors, password: undefined });
                    }
                  }}
                  secureTextEntry={!showPassword}
                  editable={!loginMutation.isPending}
                />
                <TouchableOpacity
                  style={styles.passwordIconButton}
                  onPress={() => setShowPassword(!showPassword)}
                >
                  <Lucide
                    name={showPassword ? 'eye-off' : 'eye'}
                    size={FONT_SIZES.lg}
                    color={COLORS.placeholder}
                  />
                </TouchableOpacity>
              </View>
              {errors.password && (
                <Text style={[styles.errorText, { color: COLORS.danger }]}>
                  {errors.password}
                </Text>
              )}
            </View>
          </View>

          {/* Login Button */}
          <Button
            label={loginMutation.isPending ? 'Logging in...' : 'Login'}
            variant="primary"
            size="large"
            onPress={handleLogin}
            disabled={loginMutation.isPending}
          />

          {/* Register Link */}
          <View style={styles.footer}>
            <Text style={styles.footerText}>Don't have an account? </Text>
            <TouchableOpacity onPress={() => navigation.navigate('Register')}>
              <Text style={styles.footerLink}>Sign Up</Text>
            </TouchableOpacity>
          </View>
        </View>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
};

const styles = StyleSheet.create({
  safeArea: {
    flex: 1,
    backgroundColor: COLORS.white,
  },
  container: {
    flex: 1,
  },
  content: {
    flex: 1,
    padding: SPACING.lg,
    justifyContent: 'space-between',
  },
  header: {
    marginTop: SPACING.xl,
  },
  title: {
    fontSize: FONT_SIZES.xxxl,
    fontWeight: 'bold',
    color: COLORS.primary,
  },
  subtitle: {
    fontSize: FONT_SIZES.md,
    color: COLORS.text,
    marginTop: SPACING.sm,
    opacity: 0.6,
  },
  form: {
    gap: SPACING.lg,
  },
  inputContainer: {
    gap: SPACING.sm,
  },
  label: {
    fontSize: FONT_SIZES.md,
    fontWeight: '600',
    color: COLORS.text,
  },
  input: {
    borderWidth: 1,
    flex: 1,
    borderColor: COLORS.border,
    borderRadius: 8,
    padding: SPACING.md,
    backgroundColor: COLORS.light,
    minHeight: 48,
    fontSize: FONT_SIZES.md,
    color: COLORS.text,
  },
  passwordInputWrapper: {
    position: 'relative',
    flexDirection: 'row',
    alignItems: 'center',
  },
  passwordInput: {
    paddingRight: SPACING.xl + SPACING.md,
  },
  passwordIconButton: {
    position: 'absolute',
    right: SPACING.md,
    paddingHorizontal: SPACING.sm,
    paddingVertical: SPACING.sm,
    justifyContent: 'center',
    alignItems: 'center',
  },
  inputError: {
    borderColor: COLORS.danger,
    backgroundColor: 'rgba(255, 107, 107, 0.05)',
  },
  errorText: {
    fontSize: FONT_SIZES.sm,
  },
  footer: {
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    gap: SPACING.sm,
    marginBottom: SPACING.lg,
  },
  footerText: {
    fontSize: FONT_SIZES.md,
    color: COLORS.text,
    opacity: 0.6,
  },
  footerLink: {
    fontSize: FONT_SIZES.md,
    color: COLORS.primary,
    fontWeight: 'bold',
  },
});
